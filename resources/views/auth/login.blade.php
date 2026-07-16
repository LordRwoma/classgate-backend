<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk - ClassGate</title>
    <link rel="icon" href="{{ asset('assets/images/logo-classgate.webp') }}" type="image/webp">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-[#2b454e] antialiased bg-slate-50 overflow-hidden">
    <div class="flex h-screen w-full">

        <div class="hidden lg:block lg:w-3/5 relative bg-[#2b454e]" x-data="{ activeSlide: 0 }"
            x-init="setInterval(() => activeSlide = (activeSlide + 1) % 4, 5000)">

            <img x-show="activeSlide === 0" x-transition.opacity.duration.1000ms src="{{ asset('assets/images/slide1.webp') }}" class="absolute inset-0 w-full h-full object-cover" alt="Slide 1">
            <img x-show="activeSlide === 1" x-transition.opacity.duration.1000ms src="{{ asset('assets/images/slide2.webp') }}" class="absolute inset-0 w-full h-full object-cover" alt="Slide 2">
            <img x-show="activeSlide === 2" x-transition.opacity.duration.1000ms src="{{ asset('assets/images/slide3.webp') }}" class="absolute inset-0 w-full h-full object-cover" alt="Slide 3">
            <img x-show="activeSlide === 3" x-transition.opacity.duration.1000ms src="{{ asset('assets/images/slide4.webp') }}" class="absolute inset-0 w-full h-full object-cover" alt="Slide 4">

            <div class="absolute inset-0 bg-gradient-to-t from-[#2b454e]/90 via-[#2d5299]/30 to-transparent"></div>

            <div class="absolute bottom-8 left-8 z-20 flex flex-col justify-end items-start text-left">
                <div class="mb-4">
                    <h1 class="text-5xl font-extrabold text-white mb-2 drop-shadow-lg tracking-tight">ClassGate</h1>
                    <p class="text-lg text-blue-50 drop-shadow-md font-medium max-w-md">Asisten Pedagogi Cerdas untuk Guru Hebat.</p>
                </div>
            </div>
        </div>

        <div class="w-full lg:w-2/5 flex items-center justify-center p-6 relative">
            <div class="w-full max-w-sm">

                <div class="flex justify-start items-center gap-4 mb-4">
                    <img src="{{ asset('assets/images/logo-universitas-negeri-malang.webp') }}" alt="Universitas Negeri Malang" class="h-10 w-auto object-contain">
                    <img src="{{ asset('assets/images/logo-lidm.webp') }}" alt="LIDM" class="h-10 w-auto object-contain">
                    <img src="{{ asset('assets/images/logo-classgate.webp') }}" alt="ClassGate" class="h-10 w-auto object-contain">
                </div>

                <div class="mb-4">
                    <h2 class="text-2xl font-bold text-[#2b454e] tracking-tight">Masuk ke ClassGate</h2>
                    <p class="text-sm text-gray-500 mt-0.5">Silakan masuk untuk melanjutkan.</p>
                </div>

                <x-auth-session-status class="mb-3" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-3">
                    @csrf

                    <div>
                        <label for="email" class="block text-[#1a2e35] font-extrabold text-xs mb-1">Email Akun</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="contoh@belajar.id" 
                            class="block w-full border border-blue-200 focus:border-[#3072ed] focus:ring-[#3072ed] rounded-lg shadow-sm py-1.5 text-sm bg-[#f0f6ff] text-gray-900 placeholder-gray-400 font-medium transition-colors" />
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <label for="password" class="block text-[#1a2e35] font-extrabold text-xs">Kata Sandi</label>
                            <!-- @if (Route::has('password.request'))
                                <a class="text-[10px] font-semibold text-[#3072ed] hover:text-[#2d5299] transition-colors" href="{{ route('password.request') }}">Lupa Sandi?</a>
                            @endif -->
                        </div>
                        <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" 
                            class="block w-full border border-blue-200 focus:border-[#3072ed] focus:ring-[#3072ed] rounded-lg shadow-sm py-1.5 text-sm bg-[#f0f6ff] text-gray-900 placeholder-gray-400 font-medium transition-colors" />
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                    </div>

                    <div class="flex items-center pt-1">
                        <label for="remember_me" class="inline-flex items-center cursor-pointer">
                            <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-[#3072ed] shadow-sm focus:ring-[#3072ed]" name="remember">
                            <span class="ms-2 text-xs text-gray-600 font-bold">Ingat sesi saya</span>
                        </label>
                    </div>

                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-[#3072ed] hover:bg-[#3162bf] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#3072ed] transition-colors duration-200 mt-2">
                        Masuk ke Akun
                    </button>
                </form>

                <div class="mt-4 relative">
                    <div class="absolute inset-0 flex items-center" aria-hidden="true">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-[11px] font-medium leading-6">
                        <span class="bg-slate-50 px-3 text-gray-400">Atau masuk dengan cepat</span>
                    </div>
                </div>

                <div class="mt-3">
                    <a href="#" class="flex w-full items-center justify-center gap-3 rounded-lg bg-white px-3 py-1.5 text-sm font-semibold text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-all">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12.0003 4.75C13.7703 4.75 15.3553 5.36002 16.6053 6.54998L20.0303 3.125C17.9502 1.19 15.2353 0 12.0003 0C7.31028 0 3.25527 2.69 1.28027 6.60998L5.27028 9.70498C6.21525 6.86002 8.87028 4.75 12.0003 4.75Z" fill="#EA4335" />
                            <path d="M23.49 12.275C23.49 11.49 23.415 10.73 23.3 10H12V14.51H18.47C18.18 15.99 17.34 17.25 16.08 18.1L19.945 21.1C22.2 19.01 23.49 15.92 23.49 12.275Z" fill="#4285F4" />
                            <path d="M5.26498 14.2949C5.02498 13.5699 4.88501 12.7999 4.88501 11.9999C4.88501 11.1999 5.01998 10.4299 5.26498 9.7049L1.275 6.60986C0.46 8.22986 0 10.0599 0 11.9999C0 13.9399 0.46 15.7699 1.28 17.3899L5.26498 14.2949Z" fill="#FBBC05" />
                            <path d="M12.0004 24.0001C15.2404 24.0001 17.9654 22.935 19.9454 21.095L16.0804 18.095C15.0054 18.82 13.6204 19.245 12.0004 19.245C8.8704 19.245 6.21537 17.135 5.26538 14.29L1.27539 17.385C3.25539 21.31 7.3104 24.0001 12.0004 24.0001Z" fill="#34A853" />
                        </svg>
                        <span class="text-[#2b454e]">Lanjutkan dengan Google</span>
                    </a>
                </div>

                <div class="mt-4 text-center">
                    <p class="text-xs text-gray-600 mb-1">
                        Belum bergabung dengan ClassGate? 
                        <a href="{{ route('register') }}" class="font-bold text-[#3072ed] hover:text-[#2d5299] hover:underline transition-all">Daftar akun baru</a>
                    </p>
                    <p class="text-[10px] text-gray-400">Website ini dibuat oleh Tim Workshop Edifice 2 - LIDM ITDP</p>
                </div>

            </div>
        </div>

    </div>
</body>
</html>