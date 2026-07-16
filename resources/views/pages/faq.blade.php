<x-classgate-layout title="Bantuan & FAQ" active="">

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12" x-data="{ open: 'apa-itu' }">
        <div class="text-center mb-10">
            <h1 class="text-3xl font-extrabold text-[#2b454e] mb-2">Bantuan & FAQ</h1>
            <p class="text-gray-500">Cara pakai ClassGate & pertanyaan yang sering ditanyakan.</p>
        </div>

        {{-- Langkah-langkah pakai fitur utama --}}
        <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 mb-10">
            <h2 class="text-lg font-bold text-[#2b454e] mb-6">Cara Membuat Learning Kit dalam 3 Langkah</h2>
            <div class="grid sm:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="w-12 h-12 bg-[#f0f6ff] text-[#3072ed] font-extrabold rounded-2xl flex items-center justify-center mx-auto mb-3">1</div>
                    <p class="font-bold text-[#2b454e] text-sm mb-1">Isi Parameter</p>
                    <p class="text-xs text-gray-500">Pilih mata pelajaran, fase & kelas, topik, alokasi waktu, dan tujuan pembelajaran lewat dropdown sederhana.</p>
                </div>
                <div class="text-center">
                    <div class="w-12 h-12 bg-[#f0f6ff] text-[#3072ed] font-extrabold rounded-2xl flex items-center justify-center mx-auto mb-3">2</div>
                    <p class="font-bold text-[#2b454e] text-sm mb-1">AI Menyusun 4 Dokumen</p>
                    <p class="text-xs text-gray-500">ClassGate otomatis membuat Modul Ajar, LKPD, Asesmen, dan Rekomendasi Pedagogis sekaligus — tinggal tunggu progres selesai.</p>
                </div>
                <div class="text-center">
                    <div class="w-12 h-12 bg-[#f0f6ff] text-[#3072ed] font-extrabold rounded-2xl flex items-center justify-center mx-auto mb-3">3</div>
                    <p class="font-bold text-[#2b454e] text-sm mb-1">Tinjau & Unduh</p>
                    <p class="text-xs text-gray-500">Cek hasilnya di 3 tab, lalu klik "Export Learning Kit" untuk mengunduh, atau simpan otomatis ke Arsip Saya.</p>
                </div>
            </div>
        </div>

        {{-- Penjelasan fitur utama --}}
        <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 mb-10">
            <h2 class="text-lg font-bold text-[#2b454e] mb-6">Fitur Utama ClassGate</h2>
            <div class="space-y-4 text-sm">
                <div class="flex gap-3"><span class="text-[#3072ed] font-bold">•</span><p><b class="text-[#2b454e]">Automated Lesson Plan Generator</b> — menyusun Modul Ajar otomatis selaras Kurikulum Merdeka.</p></div>
                <div class="flex gap-3"><span class="text-[#3072ed] font-bold">•</span><p><b class="text-[#2b454e]">Adaptive Worksheet (LKPD) Generator</b> — LKPD yang mengikuti kosakata & tujuan dari Modul Ajar yang sama.</p></div>
                <div class="flex gap-3"><span class="text-[#3072ed] font-bold">•</span><p><b class="text-[#2b454e]">Smart Assessment Builder</b> — soal & kunci jawaban yang selaras dengan tujuan pembelajaran.</p></div>
                <div class="flex gap-3"><span class="text-[#3072ed] font-bold">•</span><p><b class="text-[#2b454e]">AI Pedagogical Recommender</b> — tips mengajar & prediksi kesalahan umum siswa.</p></div>
                <div class="flex gap-3"><span class="text-[#3072ed] font-bold">•</span><p><b class="text-[#2b454e]">One-Click Export & Dashboard Management</b> — unduh & kelola semua Learning Kit dari Arsip Saya.</p></div>
            </div>
        </div>

        {{-- FAQ Accordion --}}
        <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
            <h2 class="text-lg font-bold text-[#2b454e] mb-2">Pertanyaan yang Sering Diajukan</h2>

            {{-- Highlighted question --}}
            <button @click="open = open === 'apa-itu' ? null : 'apa-itu'" class="w-full text-left mt-4 p-5 rounded-2xl bg-gradient-to-r from-[#2b454e] to-[#1a2e35] text-white transition">
                <div class="flex justify-between items-center">
                    <span class="font-bold">⭐ Apa itu ClassGate dan untuk siapa?</span>
                    <svg class="w-5 h-5 transition-transform shrink-0" :class="open === 'apa-itu' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
                <div x-show="open === 'apa-itu'" class="text-sm text-blue-100 mt-3 leading-relaxed">
                    ClassGate adalah asisten pedagogi berbasis AI khusus untuk guru Bahasa Inggris
                    Sekolah Dasar di Indonesia. Berbeda dari sekadar chat AI biasa, ClassGate memakai
                    alur input dropdown terarah (bukan bebas ketik) supaya hasilnya konsisten selaras
                    Kurikulum Merdeka, dan langsung menghasilkan 4 dokumen ajar sekaligus dalam satu proses.
                </div>
            </button>

            <div class="divide-y divide-gray-100 mt-2">
                @php
                    $faqs = [
                        'akurat' => [
                            'q' => 'Apakah hasil AI-nya bisa langsung dipakai tanpa diedit?',
                            'a' => 'Sebaiknya tetap ditinjau dulu. ClassGate menyusun draf berkualitas tinggi selaras Kurikulum Merdeka, tapi guru tetap punya konteks kelas yang lebih detail (karakter siswa, fasilitas, dsb) yang perlu disesuaikan manual.',
                        ],
                        'kenapa-dropdown' => [
                            'q' => 'Kenapa inputnya dropdown, bukan isi bebas seperti kompetitor?',
                            'a' => 'Supaya tidak membingungkan dan hasilnya tetap konsisten. ClassGate sengaja fokus pada Bahasa Inggris SD saja, jadi setiap pilihan dropdown sudah dirancang selaras fase & kelas Kurikulum Merdeka — guru tidak perlu memikirkan format prompt yang rumit.',
                        ],
                        'gagal' => [
                            'q' => 'Kenapa proses generate kadang gagal / lama?',
                            'a' => 'ClassGate memanggil model AI eksternal (Gemini) yang kadang mengalami kesibukan sesaat. Cukup klik tombol "Coba Lagi" di halaman hasil, sistem akan otomatis mengulang tanpa perlu isi ulang form.',
                        ],
                        'simpan' => [
                            'q' => 'Ke mana hasil Learning Kit saya tersimpan?',
                            'a' => 'Semua Learning Kit yang pernah dibuat otomatis tersimpan di menu "Arsip Saya", lengkap dengan fitur pencarian, unduh ulang kapan saja, dan hapus jika sudah tidak diperlukan.',
                        ],
                        'gratis' => [
                            'q' => 'Apakah ClassGate berbayar?',
                            'a' => 'Saat ini ClassGate masih dalam tahap pengembangan/riset akademik dan dapat digunakan tanpa biaya. Informasi lebih lanjut mengenai model layanan ke depan akan diumumkan melalui akun resmi.',
                        ],
                    ];
                @endphp

                @foreach ($faqs as $key => $item)
                    <div>
                        <button @click="open = open === '{{ $key }}' ? null : '{{ $key }}'" class="w-full text-left py-4 flex justify-between items-center gap-4">
                            <span class="font-semibold text-[#2b454e] text-sm">{{ $item['q'] }}</span>
                            <svg class="w-5 h-5 text-gray-400 transition-transform shrink-0" :class="open === '{{ $key }}' ? 'rotate-180 text-[#3072ed]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open === '{{ $key }}'" class="text-sm text-gray-500 leading-relaxed pb-4 pr-8">
                            {{ $item['a'] }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="text-center mt-8 text-sm text-gray-500">
            Masih punya pertanyaan? Hubungi kami di
            <a href="mailto:support@classgate.id" class="text-[#3072ed] font-semibold">support@classgate.id</a>
        </div>
    </div>
</x-classgate-layout>
