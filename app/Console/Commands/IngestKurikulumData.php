<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class IngestKurikulumData extends Command
{
    protected $signature = 'classgate:ingest-data {--fresh : Hapus progress lama dan mulai dari awal}';
    protected $description = 'Ingest buku pedoman ke Pinecone pakai embedding ASLI (gemini-embedding-001), bukan generateContent.';

    // File ini mencatat index chunk yang SUDAH berhasil, supaya kalau proses
    // gagal/berhenti di tengah jalan, jalankan ulang perintah ini tidak akan
    // mengulang chunk yang sudah sukses (hemat kuota API).
    private string $progressFile = 'ingest-progress.json';

    public function handle()
    {
        if ($this->option('fresh') && Storage::exists($this->progressFile)) {
            Storage::delete($this->progressFile);
            $this->info('Progress lama dihapus, mulai dari awal.');
        }

        $done = Storage::exists($this->progressFile)
            ? json_decode(Storage::get($this->progressFile), true)
            : [];

        $path = 'data_kemendikbud.txt';
        if (!Storage::exists($path)) {
            $this->error("File {$path} tidak ditemukan di storage/app!");
            return;
        }

        $rawText = Storage::get($path);
        $chunks = array_values(array_filter(
            preg_split('/\n\s*\n/', $rawText),
            fn($chunk) => strlen(trim($chunk)) > 100
        ));

        $this->info('Total potongan: ' . count($chunks) . ' | Sudah selesai sebelumnya: ' . count($done));

        $geminiKey = env('GEMINI_API_KEY');
        // Endpoint EMBEDDING asli - bukan generateContent - kuotanya jauh lebih longgar
        // dan hasilnya vector matematis sungguhan, bukan hasil "karangan" model chat.
        $embedUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-embedding-001:embedContent?key={$geminiKey}";

        $bar = $this->output->createProgressBar(count($chunks));

        foreach ($chunks as $index => $chunkText) {
            if (in_array($index, $done)) {
                $bar->advance();
                continue; // sudah pernah sukses, jangan ulang -> hemat kuota
            }

            try {
                $response = Http::withoutVerifying()
                    ->timeout(60)
                    ->post($embedUrl, [
                        'model' => 'models/gemini-embedding-001',
                        'content' => ['parts' => [['text' => trim($chunkText)]]],
                        'outputDimensionality' => 768,
                    ]);

                if (!$response->successful()) {
                    $this->newLine();
                    $this->error("Gagal embed potongan #{$index}: " . $response->body());
                    // Berhenti total di sini kalau ini indikasi quota habis (HTTP 429),
                    // supaya sisa kuota harian tidak langsung terbakar oleh retry beruntun.
                    if ($response->status() === 429) {
                        $this->warn('Kuota API kemungkinan habis (HTTP 429). Berhenti dulu, coba lagi nanti / besok.');
                        break;
                    }
                    continue;
                }

                $vector = $response->json('embedding.values');

                if (!$vector) {
                    $this->newLine();
                    $this->error("Respons embedding kosong untuk potongan #{$index}.");
                    continue;
                }

                $pinecone = Http::withHeaders([
                        'Api-Key' => env('PINECONE_API_KEY'),
                        'Content-Type' => 'application/json',
                    ])
                    ->withoutVerifying()
                    ->timeout(60)
                    ->post('https://' . env('PINECONE_HOST') . '/vectors/upsert', [
                        'vectors' => [[
                            'id' => 'kb_' . $index,
                            'values' => $vector,
                            'metadata' => ['text' => substr(trim($chunkText), 0, 1000)],
                        ]],
                        'namespace' => 'kurikulum-merdeka',
                    ]);

                if ($pinecone->successful()) {
                    $done[] = $index;
                    Storage::put($this->progressFile, json_encode($done));
                } else {
                    $this->newLine();
                    $this->error("Gagal upsert Pinecone potongan #{$index}: " . $pinecone->body());
                }

                // Jeda kecil supaya tidak menabrak rate-limit per-menit (RPM).
                usleep(300000); // 0.3 detik

            } catch (\Exception $e) {
                $this->newLine();
                $this->error("Error potongan #{$index}: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Selesai. Total tersimpan: ' . count($done) . ' / ' . count($chunks));
        $this->comment('Kalau masih ada sisa yang gagal, jalankan ulang "php artisan classgate:ingest-data" tanpa --fresh untuk melanjutkan dari yang belum selesai.');
    }
}
