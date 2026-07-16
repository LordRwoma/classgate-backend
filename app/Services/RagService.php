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
}
