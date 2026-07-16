<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class RagService
{
    /**
     * Ambil potongan referensi paling relevan dari buku pedoman (Pinecone)
     * berdasarkan topik yang diinput guru. Total HANYA 1 panggilan embedding
     * (bukan generateContent) + 1 panggilan Pinecone query - sangat ringan
     * untuk kuota, tidak seperti proses ingest yang sudah beres di awal.
     */
    public function retrieveContext(string $query, int $topK = 3): string
    {
        $geminiKey = env('GEMINI_API_KEY');
        $embedUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-embedding-001:embedContent?key={$geminiKey}";

        try {
            $embedResponse = Http::withoutVerifying()->timeout(30)->post($embedUrl, [
                'model' => 'models/gemini-embedding-001',
                'content' => ['parts' => [['text' => $query]]],
                'outputDimensionality' => 768,
            ]);

            if (!$embedResponse->successful()) {
                return ''; // gagal ambil konteks -> AI tetap jalan tanpa RAG (graceful fallback)
            }

            $vector = $embedResponse->json('embedding.values');
            if (!$vector) {
                return '';
            }

            $pineconeResponse = Http::withHeaders([
                    'Api-Key' => env('PINECONE_API_KEY'),
                    'Content-Type' => 'application/json',
                ])
                ->withoutVerifying()
                ->timeout(30)
                ->post('https://' . env('PINECONE_HOST') . '/query', [
                    'vector' => $vector,
                    'topK' => $topK,
                    'namespace' => 'kurikulum-merdeka',
                    'includeMetadata' => true,
                ]);

            if (!$pineconeResponse->successful()) {
                return '';
            }

            $matches = $pineconeResponse->json('matches') ?? [];

            return collect($matches)
                ->pluck('metadata.text')
                ->filter()
                ->implode("\n---\n");

        } catch (\Throwable $e) {
            report($e);
            return ''; // RAG gagal -> jangan gagalkan seluruh generate, cukup jalan tanpa konteks tambahan
        }
    }

    /**
     * Fungsi baru untuk men-generate Modul Ajar, LKPD, dan Asesmen
     * Menggunakan instruksi khusus (Prompt Engineering) untuk mematuhi 
     * standar Validasi Ahli Materi LIDM 2026.
     */
    public function generateModule(string $userQuery, string $context): string
    {
        $apiKey = env('GEMINI_API_KEY');
        // Menggunakan model generative untuk menyusun teks
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$apiKey}";
        
        $prompt = "Anda adalah Ahli Pedagogi Bahasa Inggris untuk Sekolah Dasar pada Kurikulum Merdeka. 
        Gunakan informasi dari dokumen referensi berikut untuk merancang pembelajaran yang inovatif dan terstruktur.
        
        REFERENSI KURIKULUM:
        {$context}

        TUGAS:
        Buatlah perencanaan pembelajaran Bahasa Inggris berdasarkan permintaan guru berikut: '{$userQuery}'

        INSTRUKSI FORMAT & KUALITAS (WAJIB DIIKUTI UNTUK VALIDASI):
        1. STRUKTUR: Bagi output menjadi 3 bagian jelas dengan sub-judul: [MODUL AJAR], [LEMBAR KERJA PESERTA DIDIK - LKPD], dan [ASESMEN].
        2. KESESUAIAN MATERI: Pastikan materi, kosakata, dan aktivitas sangat sesuai dengan karakteristik, usia, dan tingkat kognitif siswa Sekolah Dasar. Kedalaman materi harus pas, tidak terlalu rumit.
        3. KEAKURATAN ISI: Gunakan istilah pedagogi Kurikulum Merdeka yang tepat (Fase, Capaian Pembelajaran, Tujuan Pembelajaran, Alur Tujuan Pembelajaran, Profil Pelajar Pancasila). Pastikan tidak ada kesalahan konsep bahasa Inggris.
        4. MANFAAT PRODUK: Buat langkah-langkah kegiatan pembelajaran yang praktis, konkret, interaktif, dan sangat mudah diterapkan oleh guru di kelas.
        5. TONE & BAHASA: Profesional, akademis, menggunakan Bahasa Indonesia yang baik dan benar (kecuali pada materi Bahasa Inggris-nya).
        
        Tuliskan langsung hasilnya dalam format Markdown yang rapi tanpa kalimat pengantar atau penutup.";

        try {
            $response = Http::withoutVerifying()->timeout(120)->post($url, [
                'contents' => [['parts' => [['text' => $prompt]]]]
            ]);

            if ($response->successful()) {
                return $response->json('candidates.0.content.parts.0.text') ?? 'Gagal menghasilkan teks dari AI.';
            }

            return "Maaf, terjadi kesalahan saat menghubungi AI: " . $response->body();

        } catch (\Exception $e) {
            return "Terjadi kesalahan sistem: " . $e->getMessage();
        }
    }
}