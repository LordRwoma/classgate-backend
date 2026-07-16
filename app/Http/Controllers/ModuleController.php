<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Module;
use App\Services\RagService;
use App\Services\DimensionPromptMapper;
use App\Services\ImageGenerationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ModuleController extends Controller
{
    private string $apiUrl;

    public function __construct(private RagService $rag, private ImageGenerationService $imageGen)
    {
        $apiKey = env('GEMINI_API_KEY');
        $this->apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}";
    }

    public function create()
    {
        return view('modules.create', ['dimensions' => DimensionPromptMapper::DIMENSIONS]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'phase' => 'required|string|max:50',
            'topic' => 'required|string|max:255',
            'duration' => 'required|string|max:100',
            'learning_objectives' => 'required|string',
            'assessment_type' => 'required|in:sumatif,formatif,diagnostik',
            'dimensions' => 'nullable|array|max:3',
            'dimensions.*' => 'in:' . implode(',', array_keys(DimensionPromptMapper::DIMENSIONS)),
            // [FITUR OPSI 1 & 2] Checkbox di form pembuatan modul. Checkbox yang
            // tidak dicentang tidak akan dikirim browser sama sekali, karenanya
            // 'nullable' + fallback boolean() di bawah (bukan 'required').
            'include_game' => 'nullable|boolean',
            'include_flashcard' => 'nullable|boolean',
        ]);

        $module = Module::create([
            'user_id' => Auth::id(),
            ...$validated,
            'include_game' => $request->boolean('include_game'),
            'include_flashcard' => $request->boolean('include_flashcard'),
            'status' => 'processing',
        ]);

        return redirect()->route('modules.show', $module);
    }

    public function show(Module $module)
    {
        abort_if($module->user_id !== Auth::id(), 403);
        return view('modules.show', compact('module'));
    }

    public function retry(Module $module)
    {
        abort_if($module->user_id !== Auth::id(), 403);
        $module->update([
            'status' => 'processing',
            'generated_content' => null, 'generated_lkpd' => null,
            'generated_assessment' => null, 'generated_tips' => null,
        ]);
        return redirect()->route('modules.show', $module);
    }

    public function generateStep(Request $request, Module $module)
    {
        abort_if($module->user_id !== Auth::id(), 403);
        $step = $request->input('step');

        try {
            if ($step === 'modul_ajar') {
                $context = $this->rag->retrieveContext("{$module->topic} {$module->phase} {$module->subject}");
                $referensi = $context
                    ? "\n\nGunakan referensi resmi berikut sebagai dasar materi (jangan menyimpang darinya):\n---\n{$context}\n---\n"
                    : '';

                $raw = $this->callGemini($this->buildCombinedPrompt($module, $referensi));
                $parsed = $this->parseCombinedOutput($raw);

                $module->update([
                    'generated_content' => $parsed['modul_ajar'],
                    'generated_lkpd' => $parsed['lkpd'],
                    'generated_assessment' => $parsed['asesmen'],
                    'generated_tips' => $parsed['tips'],
                ]);

                return response()->json(['success' => true]);
            }

            if (in_array($step, ['lkpd', 'asesmen', 'tips'])) {
                if ($step === 'tips') {
                    $module->update(['status' => 'completed']);
                }
                return response()->json(['success' => true]);
            }

            return response()->json(['message' => 'Tahap tidak dikenal.'], 422);
        } catch (\Throwable $e) {
            Log::error("Gagal generate tahap '{$step}': " . $e->getMessage(), ['module_id' => $module->id]);
            $module->update(['status' => 'error']);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * [BARU] Adaptive Worksheet - versi Game HTML Interaktif. Dipisah dari
     * generateStep() dan dipicu tombol terpisah di halaman hasil, supaya:
     * (1) guru yang tidak butuh game tidak ikut menanggung biaya API-nya,
     * (2) kalau gagal, tidak menggagalkan Modul Ajar/LKPD teks yang sudah jadi.
     */
    public function generateGame(Module $module)
    {
        abort_if($module->user_id !== Auth::id(), 403);
        // [FITUR OPSI 1] Guru wajib mencentang opsi ini saat membuat modul.
        abort_unless($module->include_game, 422, 'Fitur Game HTML tidak diaktifkan untuk modul ini.');

        try {
            $html = $this->callGemini($this->buildGamePrompt($module));
            $html = $this->stripCodeFence($html);
            // [FIX BUG 2] Game HTML ini akan dibuka lewat iframe srcdoc DAN
            // diunduh sebagai file .html mandiri (lihat downloadGame()). Karena
            // isolated/self-contained, ia TIDAK BISA memuat gambar lewat URL
            // relatif ke storage server. Solusinya: tanamkan gambar flashcard
            // yang sudah ada (generated_media) langsung sebagai Base64 Data URI
            // ke dalam markup <img data-vocab="..."> yang diminta AI hasilkan.
            $html = $this->embedGameImages($html, $module);

            $module->update(['generated_lkpd_game' => $html]);

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            Log::error('Gagal generate game LKPD: ' . $e->getMessage(), ['module_id' => $module->id]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * [FIX BUG 2] Cari semua <img data-vocab="label"> yang diminta AI tulis di
     * buildGamePrompt(), lalu ganti src-nya dengan Base64 Data URI dari file
     * flashcard yang sudah tersimpan di storage (jika labelnya cocok, tanpa
     * memandang huruf besar/kecil). Kalau tidak ada flashcard yang cocok,
     * tag <img> dihapus supaya tidak menyisakan ikon "gambar rusak" di game.
     */
    private function embedGameImages(string $html, Module $module): string
    {
        $mediaByLabel = collect($module->generated_media ?? [])
            ->keyBy(fn ($item) => \Illuminate\Support\Str::lower(trim($item['label'] ?? '')));

        return preg_replace_callback(
            '/<img\b[^>]*\bdata-vocab=["\']([^"\']+)["\'][^>]*>/i',
            function (array $m) use ($mediaByLabel) {
                $label = \Illuminate\Support\Str::lower(trim($m[1]));
                $item = $mediaByLabel->get($label);

                if (!$item || !\Storage::disk('public')->exists($item['url'])) {
                    return ''; // tidak ada aset -> hapus tag drpd tampil rusak
                }

                $binary = \Storage::disk('public')->get($item['url']);
                $mime = \Storage::disk('public')->mimeType($item['url']) ?: 'image/png';
                $dataUri = "data:{$mime};base64," . base64_encode($binary);

                return '<img src="' . $dataUri . '" alt="' . e($m[1]) . '" style="max-width:100%;height:auto;">';
            },
            $html
        );
    }

    /**
     * [BARU] AI Image Generation untuk Media Ajar / Flashcard. On-demand per
     * kata/istilah yang dipilih guru, bukan otomatis massal.
     */
    public function generateImage(Request $request, Module $module)
    {
        abort_if($module->user_id !== Auth::id(), 403);
        // [FITUR OPSI 2] Guru wajib mencentang opsi ini saat membuat modul.
        abort_unless($module->include_flashcard, 422, 'Fitur Media Ajar/Flashcard tidak diaktifkan untuk modul ini.');

        $validated = $request->validate([
            'label' => 'required|string|max:100',
        ]);

        $path = $this->imageGen->generate($validated['label']);

        if (!$path) {
            return response()->json(['success' => false, 'message' => 'Gagal membuat gambar. Coba lagi.'], 500);
        }

        $media = $module->generated_media ?? [];
        $media[] = ['type' => 'flashcard', 'label' => $validated['label'], 'url' => $path];
        $module->update(['generated_media' => $media]);

        return response()->json(['success' => true, 'url' => \Storage::disk('public')->url($path), 'label' => $validated['label']]);
    }

    public function deleteMedia(Module $module, int $index)
    {
        abort_if($module->user_id !== Auth::id(), 403);

        $media = $module->generated_media ?? [];
        if (isset($media[$index])) {
            \Storage::disk('public')->delete($media[$index]['url']);
            unset($media[$index]);
            $module->update(['generated_media' => array_values($media)]);
        }

        return response()->json(['success' => true]);
    }

    private function buildCombinedPrompt(Module $module, string $referensi): string
    {
        $dimensiBlock = DimensionPromptMapper::buildPromptBlock($module->dimensions ?? []);
        $asesmenInstruksi = $this->assessmentTypeInstruction($module->assessment_type);

        return <<<PROMPT
Kamu adalah asisten penyusun perangkat ajar Bahasa Inggris SD mengikuti struktur
Kurikulum Merdeka. Gunakan istilah "murid" untuk menyebut peserta pembelajaran -
DILARANG menggunakan kata "siswa" maupun "peserta didik" di seluruh dokumen.

Parameter:
- Mata Pelajaran: {$module->subject}
- Fase & Kelas: {$module->phase}
- Topik: {$module->topic}
- Alokasi Waktu: {$module->duration}
- Tujuan Pembelajaran: {$module->learning_objectives}
- Jenis Asesmen: {$module->assessment_type}
{$referensi}{$dimensiBlock}
Susun EMPAT dokumen sekaligus dalam SATU balasan, dipisahkan PERSIS dengan penanda
di bawah ini (penanda harus ditulis apa adanya, tanpa markdown tambahan):

===MODUL_AJAR===
(HTML sederhana: <h3>, <ol>, <p> saja. Isi: Identitas modul, Tujuan Pembelajaran
dalam poin-poin, Kegiatan Pembelajaran (Apersepsi, Inti, Penutup) dengan estimasi
menit yang totalnya sama dengan alokasi waktu. Sebut murid, bukan murid.)

===LKPD===
(HTML sederhana, selaras dengan Modul Ajar di atas. Minimal 2 aktivitas murid,
individu & berpasangan/kelompok, sesuai usia {$module->phase}.)

===ASESMEN===
({$asesmenInstruksi} HTML sederhana, selaras dengan Modul Ajar di atas, lengkap
kunci jawaban di akhir.)

===TIPS===
(Teks polos TANPA HTML, maksimal 3 kalimat: prediksi kesalahan umum murid
Indonesia pada topik ini, dan satu tip pengajaran praktis.)

Balas HANYA dengan keempat bagian itu beserta penandanya, tanpa penjelasan
tambahan di luar itu.
PROMPT;
    }

    private function assessmentTypeInstruction(string $type): string
    {
        return match ($type) {
            'diagnostik' => 'Asesmen DIAGNOSTIK (dilakukan SEBELUM pembelajaran): fokus menggali pengetahuan/kosakata awal murid terkait topik ini, bukan menilai benar-salah, gunakan pertanyaan eksploratif ringan (5 soal).',
            'formatif' => 'Asesmen FORMATIF (dilakukan SELAMA pembelajaran): low-stakes, disertai umpan balik singkat di tiap soal, tujuannya memantau pemahaman bukan menghakimi nilai akhir (5 soal + catatan umpan balik per soal).',
            default => 'Asesmen SUMATIF (dilakukan DI AKHIR pembelajaran): mengukur ketercapaian Tujuan Pembelajaran secara formal, 5 soal pilihan ganda dengan bobot nilai.',
        };
    }

    private function buildGamePrompt(Module $module): string
    {
        // [FIX BUG 2] Kumpulkan label flashcard yang SUDAH punya file gambar
        // tersimpan (dari Media Ajar), supaya AI tahu kosakata mana yang boleh
        // ia ilustrasikan dengan gambar sungguhan.
        $vocabWithImage = collect($module->generated_media ?? [])
            ->pluck('label')
            ->filter()
            ->unique()
            ->values();

        $imageInstruction = $vocabWithImage->isNotEmpty()
            ? "Kosakata berikut SUDAH punya ilustrasi gambar siap pakai: "
                . $vocabWithImage->implode(', ') . ". Untuk kosakata itu SAJA, kamu BOLEH "
                . "menampilkan gambar dengan menulis persis: "
                . "<img data-vocab=\"nama_kosakata\" alt=\"nama_kosakata\">  "
                . "(atribut src TIDAK PERLU diisi, ganti \"nama_kosakata\" dengan salah satu "
                . "label di atas apa adanya). JANGAN pernah menulis <img> dengan atribut src "
                . "berisi URL/path apa pun (mis. src=\"apple.png\" atau URL eksternal) karena "
                . "game ini berjalan tanpa akses internet/server dan gambar seperti itu PASTI "
                . "akan gagal dimuat (rusak). Untuk kosakata TANPA ilustrasi siap pakai, jangan "
                . "pakai <img> sama sekali - gunakan emoji Unicode atau teks saja."
            : "JANGAN gunakan tag <img> sama sekali di game ini (tidak ada ilustrasi gambar yang "
                . "tersedia untuk modul ini) - gunakan emoji Unicode atau teks saja supaya game "
                . "tetap terlihat lengkap tanpa gambar rusak.";

        return <<<PROMPT
Berdasarkan Modul Ajar & LKPD berikut untuk murid SD Fase {$module->phase}, topik
"{$module->topic}":
---
{$module->generated_content}
{$module->generated_lkpd}
---

Buatkan SATU file HTML lengkap yang BERDIRI SENDIRI (self-contained: CSS di dalam
<style>, JavaScript di dalam <script>, TANPA dependency eksternal/CDN) berisi game
kuis interaktif sederhana untuk murid SD sesuai kosakata/materi di atas. Ketentuan:
- Minimal 5 pertanyaan (pilihan ganda atau drag-drop sederhana pakai vanilla JS)
- Ada skor yang tampil setelah menjawab semua soal
- Desain ramah anak: warna cerah, tombol besar, font mudah dibaca
- HARUS bisa dibuka langsung di browser tanpa server tambahan
- {$imageInstruction}

Balas HANYA dengan kode HTML lengkap (mulai dari <!DOCTYPE html>), tanpa
penjelasan atau markdown fence apa pun di luar kode itu.
PROMPT;
    }

    private function stripCodeFence(string $text): string
    {
        return trim(preg_replace('/^```html|^```|```$/m', '', $text));
    }

    private function parseCombinedOutput(string $raw): array
    {
        $sections = ['modul_ajar' => '', 'lkpd' => '', 'asesmen' => '', 'tips' => ''];
        $pattern = '/===MODUL_AJAR===(.*?)===LKPD===(.*?)===ASESMEN===(.*?)===TIPS===(.*)$/s';

        if (preg_match($pattern, $raw, $m)) {
            $sections['modul_ajar'] = trim($m[1]);
            $sections['lkpd'] = trim($m[2]);
            $sections['asesmen'] = trim($m[3]);
            $sections['tips'] = strip_tags(trim($m[4]));
        } else {
            $sections['modul_ajar'] = $raw;
            $sections['lkpd'] = '<p>Format LKPD tidak terbaca dari respons AI. Coba generate ulang.</p>';
            $sections['asesmen'] = '<p>Format Asesmen tidak terbaca dari respons AI. Coba generate ulang.</p>';
            $sections['tips'] = '';
        }

        return $sections;
    }

    public function history(Request $request)
    {
        $modules = Module::where('user_id', Auth::id())
            ->when($request->search, fn($q, $search) => $q->where('topic', 'like', "%{$search}%"))
            ->latest()
            ->paginate(10);

        return view('modules.history', compact('modules'));
    }

    /**
     * [FITUR BARU] One-Click Export to Printable PDF. Learning Kit lengkap
     * (Modul Ajar + LKPD + Asesmen + Media Ajar) diekspor jadi SATU file PDF
     * yang otomatis terunduh, rapi, dan siap cetak.
     *
     * Library: barryvdh/laravel-dompdf (wrapper dompdf/dompdf untuk Laravel).
     * Dipilih dibanding snappy/wkhtmltopdf karena: (1) murni PHP, tidak butuh
     * binary eksternal yang ribet di-install di server/shared hosting,
     * (2) cukup untuk layout dokumen berbasis HTML+CSS sederhana seperti ini,
     * (3) mendukung page-break-* dan Data URI gambar dengan baik.
     * composer require barryvdh/laravel-dompdf
     */
    public function download(Module $module)
    {
        abort_if($module->user_id !== Auth::id(), 403);
        abort_if($module->status !== 'completed', 404);

        // Gambar flashcard ditanam sebagai Base64 Data URI (bukan asset() URL)
        // supaya dompdf (yang me-render tanpa browser/JS dan tanpa sesi cookie
        // auth) dijamin bisa menampilkannya, betapa pun konfigurasi storage
        // server berbeda-beda antar lingkungan (lokal/produksi).
        $mediaBase64 = collect($module->generated_media ?? [])
            ->filter(fn ($item) => \Storage::disk('public')->exists($item['url'] ?? ''))
            ->map(function ($item) {
                $binary = \Storage::disk('public')->get($item['url']);
                $mime = \Storage::disk('public')->mimeType($item['url']) ?: 'image/png';
                $item['data_uri'] = "data:{$mime};base64," . base64_encode($binary);
                return $item;
            })
            ->values();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('modules.export-print', [
            'module' => $module,
            'mediaBase64' => $mediaBase64,
        ])->setPaper('a4', 'portrait')->setOptions([
            'isRemoteEnabled' => false, // tidak perlu, semua gambar sudah Data URI
            'defaultFont' => 'DejaVu Sans', // dukung karakter non-ASCII (mis. "—")
        ]);

        $filename = 'learning-kit-' . \Illuminate\Support\Str::slug($module->topic) . '-' . $module->id . '.pdf';

        return $pdf->download($filename);
    }

    // Download game LKPD sebagai file HTML mandiri
    public function downloadGame(Module $module)
    {
        abort_if($module->user_id !== Auth::id(), 403);
        abort_if(!$module->generated_lkpd_game, 404);

        return response($module->generated_lkpd_game)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'attachment; filename="game-lkpd-' . $module->id . '.html"');
    }

    public function destroy(Module $module)
    {
        abort_if($module->user_id !== Auth::id(), 403);
        $module->delete();
        return redirect()->route('modules.history')->with('success', 'Modul berhasil dihapus.');
    }

    private function callGemini(string $prompt, int $attempt = 1): string
    {
        try {
            $response = Http::withoutVerifying()->timeout(120)->connectTimeout(20)->post($this->apiUrl, [
                'contents' => [['parts' => [['text' => $prompt]]]],
                'generationConfig' => ['temperature' => 0.4],
            ]);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            if ($attempt < 3) {
                sleep(10);
                return $this->callGemini($prompt, $attempt + 1);
            }
            throw new \RuntimeException('Tidak bisa terhubung ke server Gemini: ' . $e->getMessage());
        }

        if (in_array($response->status(), [429, 503]) && $attempt < 3) {
            sleep(15);
            return $this->callGemini($prompt, $attempt + 1);
        }

        if ($response->failed()) {
            throw new \RuntimeException('Gemini API error: ' . $response->body());
        }

        $text = $response->json('candidates.0.content.parts.0.text');

        if (!$text) {
            throw new \RuntimeException('Respons Gemini kosong / format tidak sesuai: ' . $response->body());
        }

        return $text;
    }
}
