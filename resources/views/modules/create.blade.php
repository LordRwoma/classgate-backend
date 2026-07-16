<x-classgate-layout title="Buat Modul" active="buat-modul">

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-[#2b454e] mb-2">Instructional Design Engine</h1>
            <p class="text-gray-500">Isi parameter pembelajaran, ClassGate akan menyusun draf Modul Ajar untukmu.</p>
        </div>

        @if ($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 text-red-600 rounded-2xl px-6 py-4">
                <p class="font-bold mb-1">Ada input yang perlu diperbaiki:</p>
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('modules.store') }}" x-data="{ step: 1 }">
            @csrf

            {{-- Stepper --}}
            <div class="flex items-center gap-2 mb-8">
                @foreach (['Mata Pelajaran & Kelas', 'Topik & Waktu', 'Tujuan Pembelajaran', 'Asesmen & Profil Lulusan'] as $idx => $label)
                    <div class="flex items-center flex-1">
                        <div
                            class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-sm shrink-0 transition"
                            :class="step > {{ $idx + 1 }} ? 'bg-[#3072ed] text-white' : (step === {{ $idx + 1 }} ? 'bg-[#2b454e] text-white' : 'bg-gray-200 text-gray-400')"
                        >
                            <span x-show="step <= {{ $idx + 1 }}">{{ $idx + 1 }}</span>
                            <svg x-show="step > {{ $idx + 1 }}" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <p class="ml-3 mr-4 hidden sm:block text-xs font-bold uppercase tracking-wide"
                           :class="step >= {{ $idx + 1 }} ? 'text-[#2b454e]' : 'text-gray-400'">{{ $label }}</p>
                        @if (!$loop->last)
                            <div class="flex-1 h-1 rounded-full mr-4" :class="step > {{ $idx + 1 }} ? 'bg-[#3072ed]' : 'bg-gray-200'"></div>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- Step 1: Mata Pelajaran & Kelas --}}
            <div x-show="step === 1" x-cloak class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
                <h2 class="text-xl font-bold text-[#2b454e] mb-1">Langkah 1 — Mata Pelajaran & Kelas</h2>
                <p class="text-gray-500 text-sm mb-6">Tentukan mata pelajaran dan jenjang kelas yang dituju.</p>

                <div class="mb-5">
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Mata Pelajaran</label>
                    <select name="subject" class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#3072ed] bg-white">
                        <option value="Bahasa Inggris" {{ old('subject') === 'Bahasa Inggris' ? 'selected' : '' }}>Bahasa Inggris (EFL)</option>
                    </select>
                </div>

                <div class="mb-2">
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Fase & Kelas</label>
                    <select name="phase" class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#3072ed] bg-white">
                        <option value="">Pilih Fase & Kelas</option>
                        <option value="Fase A - Kelas 1" {{ old('phase') === 'Fase A - Kelas 1' ? 'selected' : '' }}>Fase A — Kelas 1</option>
                        <option value="Fase A - Kelas 2" {{ old('phase') === 'Fase A - Kelas 2' ? 'selected' : '' }}>Fase A — Kelas 2</option>
                        <option value="Fase B - Kelas 3" {{ old('phase') === 'Fase B - Kelas 3' ? 'selected' : '' }}>Fase B — Kelas 3</option>
                        <option value="Fase B - Kelas 4" {{ old('phase') === 'Fase B - Kelas 4' ? 'selected' : '' }}>Fase B — Kelas 4</option>
                        <option value="Fase C - Kelas 5" {{ old('phase') === 'Fase C - Kelas 5' ? 'selected' : '' }}>Fase C — Kelas 5</option>
                        <option value="Fase C - Kelas 6" {{ old('phase') === 'Fase C - Kelas 6' ? 'selected' : '' }}>Fase C — Kelas 6</option>
                    </select>
                </div>

                <div class="flex justify-end mt-8">
                    <button type="button" @click="step = 2" class="px-8 py-3 bg-[#3072ed] hover:bg-[#3162bf] text-white font-bold rounded-xl shadow-md transition">
                        Lanjut
                    </button>
                </div>
            </div>

            {{-- Step 2: Topik & Waktu --}}
            <div x-show="step === 2" x-cloak class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
                <h2 class="text-xl font-bold text-[#2b454e] mb-1">Langkah 2 — Topik & Alokasi Waktu</h2>
                <p class="text-gray-500 text-sm mb-6">Topik pembelajaran dan durasi pertemuan.</p>

                <div class="mb-5">
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Topik Pembelajaran</label>
                    <input type="text" name="topic" value="{{ old('topic') }}" placeholder="Contoh: Introducing Yourself, Family Members..."
                           class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#3072ed]">
                </div>

                <div class="mb-2">
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Alokasi Waktu</label>
                    <select name="duration" class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#3072ed] bg-white">
                        <option value="1 x 35 menit" {{ old('duration') === '1 x 35 menit' ? 'selected' : '' }}>1 x 35 menit</option>
                        <option value="2 x 35 menit" {{ old('duration') === '2 x 35 menit' ? 'selected' : '' }}>2 x 35 menit</option>
                        <option value="3 x 35 menit" {{ old('duration') === '3 x 35 menit' ? 'selected' : '' }}>3 x 35 menit</option>
                    </select>
                </div>

                <div class="flex justify-between mt-8">
                    <button type="button" @click="step = 1" class="px-8 py-3 border border-gray-300 text-gray-600 font-bold rounded-xl hover:bg-gray-50 transition">Kembali</button>
                    <button type="button" @click="step = 3" class="px-8 py-3 bg-[#3072ed] hover:bg-[#3162bf] text-white font-bold rounded-xl shadow-md transition">Lanjut</button>
                </div>
            </div>

            {{-- Step 3: Tujuan Pembelajaran --}}
            <div x-show="step === 3" x-cloak class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
                <h2 class="text-xl font-bold text-[#2b454e] mb-1">Langkah 3 — Tujuan Pembelajaran</h2>
                <p class="text-gray-500 text-sm mb-6">Jelaskan apa yang ingin dicapai murid di akhir pembelajaran ini.</p>

                <div class="mb-2">
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Tujuan Pembelajaran / Instruksi</label>
                    <textarea name="learning_objectives" rows="5" placeholder="Contoh: Murid dapat memperkenalkan diri secara lisan dan tulisan sederhana..."
                              class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#3072ed]">{{ old('learning_objectives') }}</textarea>
                </div>

                <div class="flex justify-between mt-8">
                    <button type="button" @click="step = 2" class="px-8 py-3 border border-gray-300 text-gray-600 font-bold rounded-xl hover:bg-gray-50 transition">Kembali</button>
                    <button type="button" @click="step = 4" class="px-8 py-3 bg-[#3072ed] hover:bg-[#3162bf] text-white font-bold rounded-xl shadow-md transition">Lanjut</button>
                </div>
            </div>

            {{-- Step 4: Jenis Asesmen & 8 Dimensi Profil Lulusan --}}
            <div x-show="step === 4" x-cloak class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100" x-data="{ selected: {{ json_encode(old('dimensions', [])) }} }">
                <h2 class="text-xl font-bold text-[#2b454e] mb-1">Langkah 4 — Asesmen & Profil Lulusan</h2>
                <p class="text-gray-500 text-sm mb-6">Pilih jenis asesmen dan (opsional) dimensi Profil Lulusan yang ingin ditekankan.</p>

                <div class="mb-6">
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Jenis Asesmen</label>
                    <select name="assessment_type" class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#3072ed] bg-white">
                        <option value="sumatif" {{ old('assessment_type') === 'sumatif' ? 'selected' : '' }}>Sumatif — menilai di akhir pembelajaran</option>
                        <option value="formatif" {{ old('assessment_type') === 'formatif' ? 'selected' : '' }}>Formatif — memantau selama pembelajaran</option>
                        <option value="diagnostik" {{ old('assessment_type') === 'diagnostik' ? 'selected' : '' }}>Diagnostik — mengecek kemampuan awal murid</option>
                    </select>
                </div>

                <div class="mb-2">
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-2">8 Dimensi Profil Lulusan <span class="font-normal normal-case text-gray-400">(opsional, maks. 3)</span></label>
                    <div class="grid sm:grid-cols-2 gap-2">
                        @foreach ($dimensions as $key => $dim)
                            <label class="flex items-start gap-2 border border-gray-200 rounded-xl p-3 cursor-pointer transition"
                                   :class="selected.includes('{{ $key }}') ? 'border-[#3072ed] bg-[#f0f6ff]' : ''">
                                <input type="checkbox" name="dimensions[]" value="{{ $key }}"
                                       x-model="selected"
                                       :disabled="!selected.includes('{{ $key }}') && selected.length >= 3"
                                       class="mt-0.5 accent-[#3072ed]">
                                <span class="text-xs font-semibold text-gray-700">{{ $dim['label'] }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- [FITUR OPSI 1 & 2] Guru memilih di sini APAKAH Game HTML dan/atau
                     Media Ajar/Flashcard ikut disiapkan. Kalau tidak dicentang, tab
                     terkait tidak akan muncul sama sekali di halaman hasil, dan API
                     AI (Gemini image-gen / game-gen) tidak akan dipanggil untuk fitur
                     itu - menghemat kuota untuk guru yang tidak membutuhkannya. --}}
                <div class="mb-2">
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-2">Fitur Tambahan <span class="font-normal normal-case text-gray-400">(opsional, dibuat belakangan lewat tombol di halaman hasil)</span></label>
                    <div class="grid sm:grid-cols-2 gap-2">
                        <label class="flex items-start gap-3 border border-gray-200 rounded-xl p-3 cursor-pointer transition hover:border-[#3072ed]">
                            <input type="checkbox" name="include_game" value="1" {{ old('include_game') ? 'checked' : '' }} class="mt-0.5 accent-[#3072ed]">
                            <span>
                                <span class="block text-xs font-bold text-gray-700">🎮 Game HTML Interaktif untuk LKPD</span>
                                <span class="block text-xs text-gray-400 mt-0.5">LKPD versi kuis interaktif yang bisa dimainkan murid langsung di browser.</span>
                            </span>
                        </label>
                        <label class="flex items-start gap-3 border border-gray-200 rounded-xl p-3 cursor-pointer transition hover:border-[#3072ed]">
                            <input type="checkbox" name="include_flashcard" value="1" {{ old('include_flashcard') ? 'checked' : '' }} class="mt-0.5 accent-[#3072ed]">
                            <span>
                                <span class="block text-xs font-bold text-gray-700">🎨 Media Ajar / Flashcard (AI Image Generation)</span>
                                <span class="block text-xs text-gray-400 mt-0.5">Buat ilustrasi flashcard kosakata dengan AI, on-demand per kata.</span>
                            </span>
                        </label>
                    </div>
                </div>

                <div class="flex justify-between mt-8">
                    <button type="button" @click="step = 3" class="px-8 py-3 border border-gray-300 text-gray-600 font-bold rounded-xl hover:bg-gray-50 transition">Kembali</button>
                    <button type="submit" class="px-8 py-3 bg-[#2b454e] hover:bg-[#1a2e35] text-white font-bold rounded-xl shadow-md transition">
                        Buat Modul Ajar
                    </button>
                </div>
                <p class="text-xs text-gray-400 mt-4">* Learning Kit lengkap (Modul Ajar, LKPD, Asesmen, Tips) akan tersimpan berstatus "Diproses" di Arsip.</p>
            </div>

        </form>
    </div>
</x-classgate-layout>
