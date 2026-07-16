<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Learning Kit - {{ $module->topic }}</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; color: #2b454e; max-width: 800px; margin: 0 auto; padding: 40px; line-height: 1.7; }
        .letterhead { display: flex; align-items: center; justify-content: space-between; border-bottom: 4px solid #3072ed; padding-bottom: 16px; margin-bottom: 8px; }
        .letterhead h1 { color: #2b454e; font-size: 22px; margin: 0; }
        .letterhead .badge { background: #f0f6ff; color: #3072ed; font-size: 11px; font-weight: 700; padding: 4px 12px; border-radius: 999px; }
        .meta-box { background: #f8fafc; border: 1px solid #eef2f7; border-radius: 12px; padding: 16px 20px; margin: 20px 0 30px; font-size: 13px; color: #555; display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
        .meta-box b { color: #2b454e; }
        h2.section { color: #fff; background: #2b454e; padding: 10px 16px; border-radius: 8px; font-size: 15px; margin-top: 36px; }
        h3 { color: #3072ed; font-size: 14px; margin-top: 20px; }
        ol, ul { padding-left: 20px; }
        li { margin-bottom: 6px; }
        table { border-collapse: collapse; width: 100%; margin: 10px 0; font-size: 13px; }
        td, th { border: 1px solid #ddd; padding: 8px; }
        th { background: #f0f6ff; }
        .tips-box { background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px; padding: 14px 18px; margin-top: 20px; font-size: 13px; color: #92400e; }
        .media-grid { display: flex; flex-wrap: wrap; gap: 14px; margin-top: 14px; }
        .media-item { width: 110px; text-align: center; }
        .media-item img { width: 110px; height: 90px; object-fit: cover; border: 1px solid #eee; border-radius: 8px; }
        .media-item span { display: block; font-size: 11px; font-weight: 700; color: #2b454e; margin-top: 4px; text-transform: capitalize; }
        .footer { margin-top: 50px; padding-top: 16px; border-top: 1px solid #eee; font-size: 11px; color: #999; text-align: center; }

        /* Page-break rules: setiap bagian utama mulai di halaman baru dan
           tidak boleh terpotong di tengah tabel/elemen kecil (rapi & siap
           cetak). dompdf mendukung page-break-* standar CSS2.1. */
        @page { margin: 24px 32px; }
        h2.section { page-break-before: always; page-break-inside: avoid; }
        h2.section:first-of-type { page-break-before: avoid; }
        table, .media-item, li { page-break-inside: avoid; }
        .tips-box { page-break-inside: avoid; }
        @media print {
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="letterhead">
        <h1>ClassGate — Learning Kit</h1>
        <span class="badge">Dihasilkan oleh AI</span>
    </div>

    <div class="meta-box">
        <div><b>Topik:</b> {{ $module->topic }}</div>
        <div><b>Mata Pelajaran:</b> {{ $module->subject }}</div>
        <div><b>Fase / Kelas:</b> {{ $module->phase }}</div>
        <div><b>Alokasi Waktu:</b> {{ $module->duration }}</div>
    </div>

    <h2 class="section">1. Modul Ajar (Lesson Plan)</h2>
    {!! $module->generated_content !!}

    <h2 class="section">2. Lembar Kerja (LKPD)</h2>
    {!! $module->generated_lkpd !!}

    <h2 class="section">3. Asesmen</h2>
    {!! $module->generated_assessment !!}

    @if (isset($mediaBase64) && $mediaBase64->isNotEmpty())
        <h2 class="section">4. Media Ajar / Flashcard Pendukung</h2>
        <div class="media-grid">
            @foreach ($mediaBase64 as $item)
                <div class="media-item">
                    <img src="{{ $item['data_uri'] }}" alt="{{ $item['label'] }}">
                    <span>{{ $item['label'] }}</span>
                </div>
            @endforeach
        </div>
    @endif

    @if ($module->generated_tips)
        <h2 class="section">{{ isset($mediaBase64) && $mediaBase64->isNotEmpty() ? '5' : '4' }}. Rekomendasi Pedagogis AI</h2>
        <div class="tips-box">💡 {{ $module->generated_tips }}</div>
    @endif

    <div class="footer">
        Dibuat otomatis oleh <b>ClassGate</b> — Asisten Pedagogi Cerdas untuk Guru Bahasa Inggris SD.<br>
        Dokumen ini adalah draf awal yang disarankan untuk ditinjau kembali oleh guru sebelum digunakan di kelas.
    </div>
</body>
</html>
