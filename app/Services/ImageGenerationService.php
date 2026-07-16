<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageGenerationService
{
    /**
     * AI Image Generation via Gemini native image-output model. Dipanggil
     * ON-DEMAND per gambar (tombol terpisah di UI), BUKAN otomatis di alur
     * generate utama - supaya tidak membengkakkan kuota tiap kali guru
     * generate 1 modul (gambar biasanya cuma dibutuhkan untuk beberapa
     * flashcard terpilih, bukan semua sekaligus).
     *
     * Model: gemini-2.0-flash-preview-image-generation (native image output).
     * Kalau butuh kualitas lebih tinggi & kamu punya akses Vertex AI, bisa
     * diganti ke Imagen 3 - struktur method ini dibuat supaya gampang di-swap.
     */
    public function generate(string $prompt, string $folder = 'media-ajar'): ?string
    {
        $apiKey = env('GEMINI_API_KEY');
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-preview-image-generation:generateContent?key={$apiKey}";

        // Prompt diarahkan supaya konsisten jadi ilustrasi edukatif untuk murid
        // sekolah dasar, bukan foto realistis (lebih aman & lebih relevan utk
        // flashcard).
        $safePrompt = "Educational flashcard illustration for elementary school English learning, "
            . "flat vector cartoon style, bright colors, simple background, no text overlay: {$prompt}";

        $response = Http::withoutVerifying()->timeout(60)->post($url, [
            'contents' => [['parts' => [['text' => $safePrompt]]]],
            'generationConfig' => [
                'responseModalities' => ['TEXT', 'IMAGE'],
            ],
        ]);

        if ($response->failed()) {
            report(new \RuntimeException('Image generation gagal: ' . $response->body()));
            return null;
        }

        $parts = $response->json('candidates.0.content.parts') ?? [];

        foreach ($parts as $part) {
            if (isset($part['inlineData']['data'])) {
                $binary = base64_decode($part['inlineData']['data']);
                $filename = "{$folder}/" . Str::uuid() . '.png';
                Storage::disk('public')->put($filename, $binary);
                return $filename; // simpan path relatif, bukan full URL
            }
        }

        return null; // model tidak mengembalikan gambar (jarang, tapi harus ditangani)
    }
}
