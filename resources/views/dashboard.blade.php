<x-classgate-layout title="Dasbor" active="beranda">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        @if (session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 font-semibold rounded-2xl px-6 py-4">
                {{ session('success') }}
            </div>
        @endif

        {{-- ============ HERO SLIDER (hook + masalah + ajakan) ============ --}}
        <div x-data="{ slide: 0, slides: 3, timer: null }" x-init="timer = setInterval(() => slide = (slide + 1) % slides, 6000)" class="relative rounded-3xl overflow-hidden shadow-xl mb-10 bg-[#2b454e]">

            <div class="relative h-[340px] lg:h-[300px]">
                {{-- Slide 1: Hook --}}
                <div x-show="slide === 0" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="absolute inset-0 flex items-center px-8 lg:px-14">
                    <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 rounded-full bg-white opacity-5"></div>
                    <div class="absolute bottom-0 right-10 w-32 h-32 rounded-full bg-[#3072ed] opacity-20 blur-2xl"></div>
                    <div class="relative z-10 lg:w-2/3">
                        <span class="inline-block text-xs font-bold text-blue-200 bg-white/10 px-3 py-1 rounded-full mb-4">Asisten Pedagogi Cerdas</span>
                        <h2 class="text-3xl lg:text-4xl font-extrabold text-white mb-4 leading-tight">Rancang Modul Ajar Lebih Cerdas & Akurat</h2>
                        <p class="text-blue-100 text-base lg:text-lg mb-6">Didukung arsitektur AI yang selaras Kurikulum Merdeka. Susun perangkat ajar Bahasa Inggris SD hanya dalam hitungan menit.</p>
                        <a href="{{ route('modules.create') }}" class="inline-block px-8 py-3 bg-[#3072ed] hover:bg-[#3162bf] text-white font-bold rounded-xl shadow-md transition-all text-sm lg:text-base">+ Mulai Buat Modul</a>
                    </div>
                </div>

                {{-- Slide 2: Masalah yang diselesaikan --}}
                <div x-show="slide === 1" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="absolute inset-0 flex items-center px-8 lg:px-14" x-cloak>
                    <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 rounded-full bg-white opacity-5"></div>
                    <div class="relative z-10 lg:w-2/3">
                        <span class="inline-block text-xs font-bold text-orange-200 bg-white/10 px-3 py-1 rounded-full mb-4">Masalah Guru Sehari-hari</span>
                        <h2 class="text-2xl lg:text-3xl font-extrabold text-white mb-4 leading-tight">Terlalu Banyak Waktu Habis untuk Administrasi, Bukan Mengajar</h2>
                        <p class="text-blue-100 text-base lg:text-lg mb-6">Menyusun Modul Ajar, LKPD, dan Asesmen satu-satu secara manual bisa memakan berjam-jam tiap minggu — waktu yang seharusnya bisa dipakai untuk fokus ke perkembangan siswa.</p>
                        <a href="{{ route('modules.create') }}" class="inline-block px-8 py-3 bg-white text-[#2b454e] font-bold rounded-xl shadow-md transition-all text-sm lg:text-base">Coba Sekarang, Gratis</a>
                    </div>
                </div>

                {{-- Slide 3: Trust / kredibilitas --}}
                <div x-show="slide === 2" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="absolute inset-0 flex items-center px-8 lg:px-14" x-cloak>
                    <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 rounded-full bg-white opacity-5"></div>
                    <div class="relative z-10 lg:w-2/3">
                        <span class="inline-block text-xs font-bold text-green-200 bg-white/10 px-3 py-1 rounded-full mb-4">Sumber Terpercaya</span>
                        <h2 class="text-2xl lg:text-3xl font-extrabold text-white mb-4 leading-tight">Selaras Panduan Resmi Kemendikdasmen</h2>
                        <p class="text-blue-100 text-base lg:text-lg mb-6">Struktur & referensi materi ClassGate disusun mengacu pada panduan resmi Kurikulum Merdeka dari Kementerian Pendidikan Dasar dan Menengah, khusus untuk mata pelajaran Bahasa Inggris jenjang SD.</p>
                        <div class="text-xs text-blue-200">Slogan kami: <span class="font-bold text-white">"Tidak Apa-Apa — ClassGate Bantu Kamu."</span></div>
                    </div>
                </div>
            </div>

            {{-- Dots --}}
            <div class="absolute bottom-5 left-1/2 -translate-x-1/2 flex gap-2 z-20">
                <template x-for="i in 3">
                    <button @click="slide = i - 1; clearInterval(timer)" class="w-2.5 h-2.5 rounded-full transition-all" :class="slide === i - 1 ? 'bg-[#3072ed] w-6' : 'bg-white/40'"></button>
                </template>
            </div>
        </div>

        {{-- ============ Keunggulan dibanding chat AI langsung ============ --}}
        <div class="mb-12">
            <div class="text-center mb-8">
                <h3 class="text-xl font-bold text-[#2b454e] mb-2">Kenapa Bukan Sekadar Tanya ChatGPT/Gemini Langsung?</h3>
                <p class="text-gray-500 text-sm max-w-2xl mx-auto">Chat AI umum memang bisa diminta bikin RPP, tapi hasilnya sering meleset dari format resmi & butuh prompt panjang tiap kali. ClassGate dirancang khusus untuk itu.</p>
            </div>
            <div class="grid sm:grid-cols-3 gap-5">
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                    <div class="w-10 h-10 bg-[#f0f6ff] text-[#3072ed] rounded-xl flex items-center justify-center mb-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                    <p class="font-bold text-[#2b454e] text-sm mb-1">Tidak Perlu Prompt Rumit</p>
                    <p class="text-xs text-gray-500">Tinggal pilih dari dropdown (fase, kelas, topik) — bukan mengetik prompt panjang tiap kali dan berharap hasilnya konsisten.</p>
                </div>
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                    <div class="w-10 h-10 bg-[#f0f6ff] text-[#3072ed] rounded-xl flex items-center justify-center mb-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <p class="font-bold text-[#2b454e] text-sm mb-1">Format Selalu Sesuai Kurikulum</p>
                    <p class="text-xs text-gray-500">Struktur Modul Ajar, LKPD, dan Asesmen sudah mengikuti kaidah Kurikulum Merdeka setiap saat, tidak tergantung cara kamu bertanya.</p>
                </div>
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                    <div class="w-10 h-10 bg-[#f0f6ff] text-[#3072ed] rounded-xl flex items-center justify-center mb-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <p class="font-bold text-[#2b454e] text-sm mb-1">4 Dokumen Sekaligus, Sinkron</p>
                    <p class="text-xs text-gray-500">Modul Ajar, LKPD, Asesmen, dan Rekomendasi Pedagogis dihasilkan bersamaan & saling selaras — bukan 4 chat terpisah yang bisa tidak nyambung.</p>
                </div>
            </div>
        </div>

        <div class="mb-12">
            <h3 class="text-xl font-bold text-[#2b454e] mb-6 text-center lg:text-left">ClassGate dalam Angka</h3>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
                <div class="bg-white rounded-2xl p-6 text-center shadow-sm border border-gray-100">
                    <h4 class="text-4xl font-extrabold text-[#3072ed] mb-1">1.2k+</h4>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Modul Dihasilkan</p>
                </div>
                <div class="bg-white rounded-2xl p-6 text-center shadow-sm border border-gray-100">
                    <h4 class="text-4xl font-extrabold text-[#3072ed] mb-1">500+</h4>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Guru Aktif</p>
                </div>
                <div class="bg-white rounded-2xl p-6 text-center shadow-sm border border-gray-100">
                    <h4 class="text-4xl font-extrabold text-[#3072ed] mb-1">12</h4>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Mata Pelajaran</p>
                </div>
                <div class="bg-white rounded-2xl p-6 text-center shadow-sm border border-gray-100">
                    <h4 class="text-4xl font-extrabold text-[#3072ed] mb-1">99%</h4>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Akurasi Pedagogi</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 mb-12">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-[#2b454e]">Modul Terbaru Saya</h3>
                <a href="{{ route('modules.history') }}" class="text-sm font-bold text-[#3072ed] hover:underline">Lihat Semua &rarr;</a>
            </div>

            @php $recent = \App\Models\Module::where('user_id', auth()->id())->latest()->take(3)->get(); @endphp

            @if ($recent->isEmpty())
                <div class="text-center py-10 text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <p>Belum ada modul yang dibuat. Yuk mulai buat modul pertamamu!</p>
                </div>
            @else
                <div class="grid sm:grid-cols-3 gap-4">
                    @foreach ($recent as $item)
                        <div class="border border-gray-100 rounded-2xl p-5 hover:shadow-md transition">
                            <div class="w-10 h-10 bg-[#f0f6ff] rounded-lg flex items-center justify-center text-[#3072ed] mb-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <p class="font-bold text-[#2b454e] mb-1">{{ $item->topic }}</p>
                            <p class="text-xs text-gray-400 mb-3">{{ $item->phase }} &middot; {{ $item->created_at->translatedFormat('d M Y') }}</p>
                            <span class="text-xs font-semibold px-3 py-1 rounded-full {{ $item->status === 'processing' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700' }}">
                                {{ $item->status === 'processing' ? 'Diproses' : 'Selesai' }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 mb-12">
            <div class="flex flex-col lg:flex-row items-center gap-8">
                <div class="lg:w-1/3 text-center lg:text-left">
                    <span class="inline-block text-xs font-bold text-green-600 bg-green-50 px-3 py-1 rounded-full mb-3">✓ Sumber Terpercaya</span>
                    <h3 class="text-lg font-bold text-[#2b454e] mb-2">Materi Selaras Panduan Resmi</h3>
                    <p class="text-sm text-gray-500">Struktur & referensi ClassGate mengacu pada panduan Kurikulum Merdeka dari Kemendikdasmen, khusus mata pelajaran Bahasa Inggris SD.</p>
                    <a href="https://guru.kemendikdasmen.go.id/" target="_blank" rel="noopener" class="inline-block mt-3 text-xs font-bold text-[#3072ed] hover:underline">Kunjungi Portal Guru Kemendikdasmen &rarr;</a>
                </div>
                <div class="lg:w-2/3 w-full">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4 text-center lg:text-left">Didukung Oleh</p>
                    <div class="flex flex-wrap justify-center lg:justify-start items-center gap-8 lg:gap-12 opacity-70 grayscale hover:grayscale-0 transition-all duration-500">
                        <img src="{{ asset('assets/images/logo-universitas-negeri-malang.webp') }}" alt="Universitas Negeri Malang" class="h-12 w-auto">
                        <img src="{{ asset('assets/images/logo-lidm.webp') }}" alt="LIDM" class="h-12 w-auto">
                        <div class="flex items-center gap-2 font-bold text-xl text-gray-700">
                            <span class="text-blue-500">G</span><span class="text-red-500">o</span><span class="text-yellow-500">o</span><span class="text-blue-500">g</span><span class="text-green-500">l</span><span class="text-red-500">e</span>
                            <span class="text-gray-500">Gemini</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-classgate-layout>
