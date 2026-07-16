<x-classgate-layout title="Profil & Pengaturan" active="akun">

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10"
         x-data="{
            tab: '{{ $errors->updatePassword->isNotEmpty() ? 'password' : ($errors->userDeletion->isNotEmpty() ? 'hapus' : 'info') }}',
            confirmOpen: false,
            confirmTone: 'primary',
            confirmTitle: '',
            confirmMessage: '',
            confirmLabel: 'Ya, Lanjutkan',
            confirmFormId: null,
            askConfirm(formId, title, message, tone = 'primary', label = 'Ya, Lanjutkan') {
                this.confirmFormId = formId;
                this.confirmTitle = title;
                this.confirmMessage = message;
                this.confirmTone = tone;
                this.confirmLabel = label;
                this.confirmOpen = true;
            },
            doConfirm() {
                if (this.confirmFormId) {
                    document.getElementById(this.confirmFormId).submit();
                }
                this.confirmOpen = false;
            }
         }">

        @if (session('status') === 'avatar-updated')
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 font-semibold rounded-2xl px-6 py-4 text-sm">
                Foto profil berhasil diperbarui.
            </div>
        @elseif (session('status') === 'avatar-removed')
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 font-semibold rounded-2xl px-6 py-4 text-sm">
                Foto profil berhasil dihapus.
            </div>
        @elseif (session('status') === 'profile-updated')
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 font-semibold rounded-2xl px-6 py-4 text-sm">
                Perubahan profil berhasil disimpan.
            </div>
        @elseif (session('status') === 'password-updated')
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 font-semibold rounded-2xl px-6 py-4 text-sm">
                Kata sandi berhasil diperbarui.
            </div>
        @endif

        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden lg:flex">

            {{-- ============ SIDEBAR KIRI: FOTO + MENU TAB ============ --}}
            <div class="lg:w-72 shrink-0 border-b lg:border-b-0 lg:border-r border-gray-100 p-8 flex flex-col items-center text-center">

                <div class="relative">
                    <div class="w-24 h-24 rounded-full overflow-hidden bg-[#3072ed] text-white flex items-center justify-center text-3xl font-extrabold shadow-lg ring-4 ring-[#f0f6ff]">
                        @if ($user->avatarUrl())
                            <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                        @else
                            {{ substr($user->name, 0, 1) }}
                        @endif
                    </div>

                    <form id="avatar-form" method="POST" action="{{ route('profile.avatar.update') }}" enctype="multipart/form-data" class="contents">
                        @csrf
                        <label for="avatar-input"
                               class="absolute bottom-0 right-0 w-8 h-8 bg-[#3072ed] hover:bg-[#3162bf] text-white rounded-full flex items-center justify-center shadow-md cursor-pointer transition-transform hover:scale-110 ring-2 ring-white"
                               title="Ubah foto profil">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </label>
                        <input id="avatar-input" type="file" name="avatar" accept="image/png,image/jpeg,image/webp" class="hidden"
                               onchange="document.getElementById('avatar-form').submit()">
                    </form>
                </div>

                <p class="font-extrabold text-[#2b454e] mt-4">{{ $user->name }}</p>
                <p class="text-xs text-gray-400 mb-6">{{ $user->school ? $user->school : 'Guru Bahasa Inggris SD' }}</p>

                @if ($user->avatar)
                    <form id="avatar-delete-form" method="POST" action="{{ route('profile.avatar.destroy') }}" class="mb-6 -mt-4"
                          @submit.prevent="askConfirm('avatar-delete-form', 'Hapus foto profil?', 'Foto profil kamu akan dihapus dan avatar akan kembali menampilkan inisial nama.', 'danger', 'Ya, Hapus Foto')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-[11px] font-semibold text-red-400 hover:text-red-600 hover:underline">
                            Hapus foto profil
                        </button>
                    </form>
                @endif

                <nav class="w-full space-y-1 text-left">
                    <button type="button" @click="tab = 'info'"
                            :class="tab === 'info' ? 'bg-[#f0f6ff] text-[#3072ed]' : 'text-gray-500 hover:bg-gray-50'"
                            class="w-full flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Informasi Pribadi
                    </button>
                    <button type="button" @click="tab = 'password'"
                            :class="tab === 'password' ? 'bg-[#f0f6ff] text-[#3072ed]' : 'text-gray-500 hover:bg-gray-50'"
                            class="w-full flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Login & Kata Sandi
                    </button>
                    <button type="button" @click="tab = 'hapus'"
                            :class="tab === 'hapus' ? 'bg-red-50 text-red-500' : 'text-gray-500 hover:bg-gray-50'"
                            class="w-full flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Hapus Akun
                    </button>

                    <div class="pt-1 mt-1 border-t border-gray-100">
                        <form id="logout-form" method="POST" action="{{ route('logout') }}"
                              @submit.prevent="askConfirm('logout-form', 'Keluar dari ClassGate?', 'Kamu perlu login kembali untuk mengakses dashboard dan fitur lainnya.', 'neutral', 'Ya, Keluar')">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm text-gray-500 hover:bg-gray-50 transition">
                                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Keluar
                            </button>
                        </form>
                    </div>
                </nav>
            </div>

            {{-- ============ PANEL KANAN ============ --}}
            <div class="flex-1 p-8">

                {{-- ---- TAB: INFORMASI PRIBADI ---- --}}
                <div x-show="tab === 'info'" x-cloak>
                    <h2 class="text-xl font-extrabold text-[#2b454e] mb-6">Informasi Pribadi</h2>

                    <form id="profile-info-form" method="POST" action="{{ route('profile.update') }}" x-data="{ dirty: false }" @input="dirty = true"
                          @submit.prevent="askConfirm('profile-info-form', 'Simpan perubahan profil?', 'Pastikan data yang kamu masukkan sudah benar sebelum disimpan.', 'primary', 'Ya, Simpan')">
                        @csrf
                        @method('patch')

                        <div class="grid sm:grid-cols-2 gap-5 mb-5">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Nama Lengkap</label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                       class="w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#3072ed] focus:bg-white focus:border-[#3072ed] transition text-[#2b454e] font-semibold">
                                <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">No. Telepon / WhatsApp</label>
                                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="cth. 0812xxxxxxx"
                                       class="w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#3072ed] focus:bg-white focus:border-[#3072ed] transition text-[#2b454e] font-semibold placeholder:font-normal placeholder:text-gray-400">
                                <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                            </div>
                        </div>

                        <div class="mb-5">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Email</label>
                            <div class="relative">
                                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                       class="w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-3 pr-28 focus:outline-none focus:ring-2 focus:ring-[#3072ed] focus:bg-white focus:border-[#3072ed] transition text-[#2b454e] font-semibold">
                                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && $user->hasVerifiedEmail())
                                    <span class="absolute right-4 top-1/2 -translate-y-1/2 flex items-center gap-1 text-green-600 text-xs font-bold">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        Terverifikasi
                                    </span>
                                @endif
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />

                            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 text-xs font-semibold rounded-xl px-4 py-3 mt-2">
                                    Email kamu belum terverifikasi.
                                    <form id="send-verification" method="post" action="{{ route('verification.send') }}" class="inline">
                                        @csrf
                                        <button form="send-verification" class="underline hover:text-yellow-900">Kirim ulang email verifikasi</button>
                                    </form>
                                    @if (session('status') === 'verification-link-sent')
                                        <span class="block mt-1 text-green-600">Tautan verifikasi baru telah dikirim.</span>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <div class="grid sm:grid-cols-2 gap-5 mb-5">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Nama Sekolah</label>
                                <input type="text" name="school" value="{{ old('school', $user->school) }}" placeholder="cth. SDN Klojen 1 Malang"
                                       class="w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#3072ed] focus:bg-white focus:border-[#3072ed] transition text-[#2b454e] font-semibold placeholder:font-normal placeholder:text-gray-400">
                                <x-input-error class="mt-2" :messages="$errors->get('school')" />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Kelas yang Diampu</label>
                                <input type="text" name="grade_taught" value="{{ old('grade_taught', $user->grade_taught) }}" placeholder="cth. Kelas IV & V"
                                       class="w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#3072ed] focus:bg-white focus:border-[#3072ed] transition text-[#2b454e] font-semibold placeholder:font-normal placeholder:text-gray-400">
                                <x-input-error class="mt-2" :messages="$errors->get('grade_taught')" />
                            </div>
                        </div>

                        <div class="mb-8">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Tentang Saya (Bio)</label>
                            <textarea name="bio" rows="3" placeholder="cth. Guru Bahasa Inggris yang senang eksplorasi metode belajar interaktif."
                                      class="w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#3072ed] focus:bg-white focus:border-[#3072ed] transition text-[#2b454e] font-semibold placeholder:font-normal placeholder:text-gray-400">{{ old('bio', $user->bio) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('bio')" />
                        </div>

                        <div class="flex flex-col-reverse sm:flex-row gap-3 sm:justify-end">
                            <button type="reset" class="px-8 py-3 border-2 border-[#3072ed] text-[#3072ed] font-bold rounded-xl hover:bg-[#f0f6ff] transition">
                                Batalkan Perubahan
                            </button>
                            <button type="submit" class="px-8 py-3 bg-[#3072ed] hover:bg-[#3162bf] text-white font-bold rounded-xl shadow-md transition">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>

                {{-- ---- TAB: LOGIN & KATA SANDI ---- --}}
                <div x-show="tab === 'password'" x-cloak>
                    <h2 class="text-xl font-extrabold text-[#2b454e] mb-6">Login & Kata Sandi</h2>

                    <form id="password-update-form" method="post" action="{{ route('password.update') }}" class="max-w-lg space-y-5"
                          @submit.prevent="askConfirm('password-update-form', 'Perbarui kata sandi?', 'Pastikan kamu sudah mengingat kata sandi baru sebelum melanjutkan.', 'primary', 'Ya, Perbarui')">
                        @csrf
                        @method('put')

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Kata Sandi Saat Ini</label>
                            <input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password"
                                   class="w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#3072ed] focus:bg-white focus:border-[#3072ed] transition">
                            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Kata Sandi Baru</label>
                            <input id="update_password_password" name="password" type="password" autocomplete="new-password"
                                   class="w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#3072ed] focus:bg-white focus:border-[#3072ed] transition">
                            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Konfirmasi Kata Sandi Baru</label>
                            <input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                                   class="w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#3072ed] focus:bg-white focus:border-[#3072ed] transition">
                            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
                        </div>

                        <button type="submit" class="px-8 py-3 bg-[#2b454e] hover:bg-[#1a2e35] text-white font-bold rounded-xl shadow-md transition">
                            Perbarui Kata Sandi
                        </button>
                    </form>
                </div>

                {{-- ---- TAB: HAPUS AKUN ---- --}}
                <div x-show="tab === 'hapus'" x-cloak>
                    <h2 class="text-xl font-extrabold text-[#2b454e] mb-2">Hapus Akun</h2>
                    <p class="text-sm text-gray-500 mb-6 max-w-lg">
                        Setelah akun dihapus, seluruh data dan modul yang tersimpan akan dihapus permanen dan
                        tidak dapat dikembalikan. Pastikan kamu sudah mengunduh arsip yang masih dibutuhkan
                        di menu Arsip Saya sebelum melanjutkan.
                    </p>

                    <form id="delete-account-form" method="post" action="{{ route('profile.destroy') }}" class="max-w-lg space-y-4"
                          @submit.prevent="askConfirm('delete-account-form', 'Hapus akun secara permanen?', 'Seluruh data dan modul yang tersimpan akan dihapus dan tidak dapat dikembalikan. Tindakan ini tidak bisa dibatalkan.', 'danger', 'Ya, Hapus Akun')">
                        @csrf
                        @method('delete')

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Konfirmasi Kata Sandi</label>
                            <input id="password" name="password" type="password" placeholder="Masukkan kata sandi kamu"
                                   class="w-full border border-red-200 bg-red-50/40 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-red-400 placeholder:font-normal placeholder:text-gray-400">
                            <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
                        </div>

                        <button type="submit" class="px-8 py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl shadow-md transition">
                            Ya, Hapus Akun Saya
                        </button>
                    </form>
                </div>

            </div>
        </div>

        {{-- ============ MODAL KONFIRMASI (untuk semua tindakan berisiko) ============ --}}
        <div x-show="confirmOpen" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center px-4">
            <div class="absolute inset-0 bg-[#1a2e35]/60 backdrop-blur-sm"
                 x-show="confirmOpen"
                 x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 @click="confirmOpen = false"></div>

            <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-sm p-7 text-center"
                 x-show="confirmOpen"
                 x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">

                <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4"
                     :class="{
                        'bg-red-50 text-red-500': confirmTone === 'danger',
                        'bg-[#f0f6ff] text-[#3072ed]': confirmTone === 'primary',
                        'bg-gray-100 text-gray-500': confirmTone === 'neutral'
                     }">
                    {{-- Ikon peringatan (danger) --}}
                    <svg x-show="confirmTone === 'danger'" x-cloak class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                    </svg>
                    {{-- Ikon konfirmasi biasa (primary) --}}
                    <svg x-show="confirmTone === 'primary'" x-cloak class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{-- Ikon netral (logout) --}}
                    <svg x-show="confirmTone === 'neutral'" x-cloak class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </div>

                <h3 class="text-lg font-extrabold text-[#2b454e] mb-2" x-text="confirmTitle"></h3>
                <p class="text-sm text-gray-500 mb-6 leading-relaxed" x-text="confirmMessage"></p>

                <div class="flex gap-3">
                    <button type="button" @click="confirmOpen = false"
                            class="flex-1 px-5 py-3 border-2 border-gray-200 text-gray-500 font-bold rounded-xl hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button type="button" @click="doConfirm()"
                            class="flex-1 px-5 py-3 font-bold rounded-xl shadow-md transition text-white"
                            :class="{
                                'bg-red-600 hover:bg-red-700': confirmTone === 'danger',
                                'bg-[#3072ed] hover:bg-[#3162bf]': confirmTone === 'primary',
                                'bg-[#2b454e] hover:bg-[#1a2e35]': confirmTone === 'neutral'
                            }"
                            x-text="confirmLabel">
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-classgate-layout>
