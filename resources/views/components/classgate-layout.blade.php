@props(['title' => 'ClassGate', 'active' => 'beranda'])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} - ClassGate</title>
    <link rel="icon" href="{{ asset('assets/images/logo-classgate.webp') }}" type="image/webp">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased text-[#2b454e] bg-[#f0f6ff] pb-20 lg:pb-0" x-data="{ dropdownOpen: false }">

    <header class="bg-white shadow-sm sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16 lg:h-20">

                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                    <img src="{{ asset('assets/images/logo-classgate.webp') }}" alt="ClassGate" class="h-10 w-auto">
                    <span class="text-2xl font-extrabold text-[#2b454e] tracking-tight hidden sm:block">ClassGate</span>
                </a>

                <nav class="hidden lg:flex space-x-8">
                    <a href="{{ route('dashboard') }}"
                       class="{{ $active === 'beranda' ? 'text-[#3072ed] font-bold border-b-2 border-[#3072ed]' : 'text-gray-500 hover:text-[#3072ed] font-semibold' }} px-1 py-2 transition-colors">
                        Beranda
                    </a>
                    <a href="{{ route('modules.create') }}"
                       class="{{ $active === 'buat-modul' ? 'text-[#3072ed] font-bold border-b-2 border-[#3072ed]' : 'text-gray-500 hover:text-[#3072ed] font-semibold' }} px-1 py-2 transition-colors">
                        Buat Modul
                    </a>
                    <a href="{{ route('modules.history') }}"
                       class="{{ $active === 'arsip' ? 'text-[#3072ed] font-bold border-b-2 border-[#3072ed]' : 'text-gray-500 hover:text-[#3072ed] font-semibold' }} px-1 py-2 transition-colors">
                        Arsip Saya
                    </a>
                </nav>

                <div class="relative flex items-center gap-4">
                    <span class="hidden sm:block text-sm font-bold text-gray-700">Halo, {{ Auth::user()->name }}!</span>
                    <button @click="dropdownOpen = !dropdownOpen" class="w-10 h-10 rounded-full overflow-hidden bg-[#3072ed] text-white flex items-center justify-center font-bold shadow-md hover:ring-2 hover:ring-[#3072ed] transition-all shrink-0">
                        @if (Auth::user()->avatarUrl())
                            <img src="{{ Auth::user()->avatarUrl() }}" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover">
                        @else
                            {{ substr(Auth::user()->name, 0, 1) }}
                        @endif
                    </button>

                    <div x-show="dropdownOpen" @click.away="dropdownOpen = false" x-cloak class="absolute right-0 top-12 mt-2 w-48 bg-white rounded-xl shadow-lg py-2 border border-gray-100 z-50">
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-[#f0f6ff] hover:text-[#3072ed]">Profil & Pengaturan</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm font-semibold text-red-600 hover:bg-red-50">Keluar Sistem</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main>
        {{ $slot }}
    </main>

    <footer class="bg-[#1a2e35] text-white pt-12 pb-24 lg:pb-12 mt-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <img src="{{ asset('assets/images/logo-classgate.webp') }}" alt="ClassGate" class="h-8 w-auto bg-white rounded-full p-1">
                        <span class="text-xl font-bold">ClassGate</span>
                    </div>
                    <p class="text-sm text-gray-400">Asisten Pedagogi Cerdas untuk Guru Hebat. Meringankan beban administrasi agar pendidik dapat fokus pada potensi siswa.</p>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Tautan Penting</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="{{ route('terms') }}" class="hover:text-white">Kebijakan Privasi</a></li>
                        <li><a href="{{ route('terms') }}" class="hover:text-white">Syarat & Ketentuan Pengguna</a></li>
                        <li><a href="{{ route('faq') }}" class="hover:text-white">Bantuan & FAQ</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Kontak Kami</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li>Gedung LIDM ITDP, Universitas Negeri Malang</li>
                        <li>support@classgate.id</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-700 pt-6 text-center text-xs text-gray-500">
                <p>&copy; 2026 ClassGate. Seluruh Hak Cipta Dilindungi.</p>
                <p class="mt-1">Dirancang & Dibangun dengan ❤️ oleh <span class="font-bold text-gray-400">Tim Workshop Edifice 2 - LIDM ITDP UM</span></p>
            </div>
        </div>
    </footer>

    {{-- Bottom nav mobile --}}
    <nav class="lg:hidden fixed bottom-0 w-full bg-white border-t border-gray-200 flex justify-around items-center h-16 px-2 z-50 shadow-[0_-2px_10px_rgba(0,0,0,0.05)]">
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center {{ $active === 'beranda' ? 'text-[#3072ed]' : 'text-gray-400 hover:text-[#3072ed]' }} transition-colors">
            <svg class="w-6 h-6 mb-1" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
            <span class="text-[10px] font-bold">Beranda</span>
        </a>
        <a href="{{ route('modules.create') }}" class="flex flex-col items-center {{ $active === 'buat-modul' ? 'text-[#3072ed]' : 'text-gray-400 hover:text-[#3072ed]' }} transition-colors">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
            <span class="text-[10px] font-semibold">Buat</span>
        </a>
        <a href="{{ route('modules.history') }}" class="flex flex-col items-center {{ $active === 'arsip' ? 'text-[#3072ed]' : 'text-gray-400 hover:text-[#3072ed]' }} transition-colors">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
            <span class="text-[10px] font-semibold">Arsip</span>
        </a>
        <a href="{{ route('profile.edit') }}" class="flex flex-col items-center {{ $active === 'akun' ? 'text-[#3072ed]' : 'text-gray-400 hover:text-[#3072ed]' }} transition-colors">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            <span class="text-[10px] font-semibold">Akun</span>
        </a>
    </nav>

    <a href="#" class="fixed bottom-24 lg:bottom-8 right-6 z-50 bg-[#2b454e] hover:bg-[#1a2e35] text-white p-4 rounded-full shadow-2xl transition-transform hover:scale-110 flex items-center justify-center group">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
        </svg>
        <span class="absolute right-16 bg-gray-900 text-white text-xs font-bold px-3 py-1.5 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap hidden lg:block shadow-lg">
            Chat AI B. Inggris SD
        </span>
    </a>

</body>
</html>
