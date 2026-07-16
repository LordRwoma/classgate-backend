<?php

namespace App\Services;

class DimensionPromptMapper
{
    /**
     * 8 Dimensi Profil Lulusan -> instruksi konkret untuk AI, BUKAN sekadar
     * nama dimensi. Ini yang membuat dimensi benar-benar termanifestasi di
     * kegiatan pembelajaran, bukan cuma disebut di label.
     */
    public const DIMENSIONS = [
        'keimanan' => [
            'label' => 'Keimanan dan Ketakwaan terhadap Tuhan Yang Maha Esa',
            'instruksi' => 'Sisipkan 1 momen refleksi singkat (misalnya rasa syukur atas kemampuan berbahasa) di bagian Apersepsi atau Penutup, tanpa terkesan dipaksakan.',
        ],
        'kewargaan' => [
            'label' => 'Kewargaan',
            'instruksi' => 'Kaitkan materi dengan konteks hidup bermasyarakat/lingkungan sekitar murid (contoh: kosakata gotong royong, aturan sederhana di rumah/sekolah).',
        ],
        'penalaran_kritis' => [
            'label' => 'Penalaran Kritis',
            'instruksi' => 'Tambahkan minimal 1 pertanyaan HOTS (why/how, bukan sekadar what) di Kegiatan Inti atau Asesmen.',
        ],
        'kreativitas' => [
            'label' => 'Kreativitas',
            'instruksi' => 'Sertakan 1 aktivitas terbuka (open-ended) di mana murid bisa memilih/membuat jawabannya sendiri, bukan hanya mengisi jawaban tunggal yang pasti.',
        ],
        'kolaborasi' => [
            'label' => 'Kolaborasi',
            'instruksi' => 'Pastikan minimal 1 aktivitas dikerjakan berpasangan/berkelompok (pair work/group work), bukan seluruhnya individu.',
        ],
        'kemandirian' => [
            'label' => 'Kemandirian',
            'instruksi' => 'Sertakan 1 aktivitas yang murid kerjakan sendiri tanpa instruksi langkah-demi-langkah dari guru (self-directed task).',
        ],
        'kesehatan' => [
            'label' => 'Kesehatan',
            'instruksi' => 'Jika relevan dengan topik, selipkan kosakata/konteks pola hidup sehat; jika tidak relevan, sisipkan 1 jeda gerak (brain-break) singkat di tengah Kegiatan Inti.',
        ],
        'komunikasi' => [
            'label' => 'Komunikasi',
            'instruksi' => 'Pastikan ada aktivitas speaking/presentasi lisan singkat (bukan hanya menulis/memilih jawaban).',
        ],
    ];

    /** Bangun blok instruksi untuk disuntik ke prompt, hanya untuk dimensi yang dicentang guru. */
    public static function buildPromptBlock(array $selectedKeys): string
    {
        if (empty($selectedKeys)) {
            return '';
        }

        $lines = collect($selectedKeys)
            ->filter(fn ($key) => isset(self::DIMENSIONS[$key]))
            ->map(fn ($key) => "- " . self::DIMENSIONS[$key]['label'] . ": " . self::DIMENSIONS[$key]['instruksi'])
            ->implode("\n");

        if (!$lines) {
            return '';
        }

        return <<<TXT

INTEGRASI 8 DIMENSI PROFIL LULUSAN (wajib termanifestasi nyata di kegiatan, JANGAN
hanya menulis ulang nama dimensinya di teks):
{$lines}

TXT;
    }
}
