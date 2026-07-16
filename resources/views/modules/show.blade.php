<x-classgate-layout title="Hasil Modul" active="buat-modul">

    <div
        class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10"
        x-data="learningKitProgress({{ $module->id }}, '{{ $module->status }}')"
        x-init="init()"
    >

        <a href="{{ route('modules.history') }}" class="text-sm font-bold text-[#3072ed] hover:underline mb-4 inline-block">&larr; Kembali ke Arsip</a>

        <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 mb-6">
            <div class="flex items-start justify-between mb-2">
                <div>
                    <h1 class="text-2xl font-extrabold text-[#2b454e]">{{ $module->topic }}</h1>
                    <p class="text-gray-500 text-sm">{{ $module->subject }} &middot; {{ $module->phase }} &middot; {{ $module->duration }}</p>
                </div>
                <span class="text-xs font-bold px-3 py-1.5 rounded-full shrink-0"
                    :class="{
                        'bg-yellow-100 text-yellow-700': status === 'processing',
                        'bg-green-100 text-green-700': status === 'completed',
                        'bg-red-100 text-red-700': status === 'error',
                    }"
                    x-text="{processing: 'Sedang Diproses', completed: 'Selesai', error: 'Gagal'}[status]"
                ></span>
            </div>
        </div>

        {{-- STATE: Processing — popup step-by-step --}}
        <div x-show="status === 'processing'" x-cloak class="bg-white rounded-3xl p-10 shadow-sm border border-gray-100 text-center">
            <svg class="animate-spin w-10 h-10 text-[#3072ed] mx-auto mb-5" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
            </svg>

            <p class="font-bold text-[#2b454e] text-lg mb-6" x-text="currentLabel"></p>

            {{-- Checklist 4 tahap --}}
            <div class="max-w-xs mx-auto text-left space-y-3">
                <template x-for="(item, idx) in steps" :key="item.key">
                    <div class="flex items-center gap-3">
                        <div class="w-6 h-6 rounded-full flex items-center justify-center shrink-0"
                             :class="stepIndex > idx ? 'bg-green-500' : (stepIndex === idx ? 'bg-[#3072ed]' : 'bg-gray-200')">
                            <svg x-show="stepIndex > idx" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            <svg x-show="stepIndex === idx" class="w-3 h-3 text-white animate-pulse" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="8"/></svg>
                        </div>
                        <span class="text-sm font-semibold" :class="stepIndex >= idx ? 'text-[#2b454e]' : 'text-gray-400'" x-text="item.label"></span>
                    </div>
                </template>
            </div>

            <p class="text-xs text-gray-400 mt-6">Jangan tutup atau muat ulang halaman ini selama proses berjalan.</p>
        </div>

        {{-- STATE: Error --}}
        <div x-show="status === 'error'" x-cloak class="bg-white rounded-3xl p-10 shadow-sm border border-gray-100 text-center">
            <svg class="w-10 h-10 text-red-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z"/></svg>
            <p class="font-bold text-[#2b454e] mb-1">AI gagal menyusun modul ini.</p>
            <p class="text-sm text-gray-400 mb-2" x-text="errorMsg"></p>
            <p class="text-xs text-gray-400 mb-6">Cek <code class="bg-gray-100 px-1.5 py-0.5 rounded">storage/logs/laravel.log</code> untuk detail teknisnya.</p>
            <button @click="retry()" class="px-6 py-3 bg-[#3072ed] hover:bg-[#3162bf] text-white font-bold rounded-xl shadow-md transition">
                Coba Lagi
            </button>
        </div>

        {{-- STATE: Completed --}}
        <div x-show="status === 'completed'" x-cloak x-data="{ tab: 'modul' }">

            <div x-show="tips" class="bg-gradient-to-r from-yellow-50 to-orange-50 border border-yellow-200 rounded-2xl p-5 mb-6 flex gap-4 items-start">
                <div class="w-9 h-9 bg-yellow-400 rounded-xl flex items-center justify-center shrink-0 text-lg">💡</div>
                <div>
                    <p class="font-bold text-yellow-800 text-sm mb-1">Rekomendasi Pedagogis AI</p>
                    <p class="text-sm text-yellow-800/90 leading-relaxed" x-text="tips"></p>
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                <div class="flex gap-1 bg-gray-100 rounded-xl p-1">
                    <button @click="tab = 'modul'" :class="tab === 'modul' ? 'bg-white shadow-sm text-[#3072ed]' : 'text-gray-500'" class="px-4 py-2 rounded-lg text-sm font-bold transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Modul Ajar
                    </button>
                    <button @click="tab = 'lkpd'" :class="tab === 'lkpd' ? 'bg-white shadow-sm text-[#3072ed]' : 'text-gray-500'" class="px-4 py-2 rounded-lg text-sm font-bold transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        LKPD
                    </button>
                    <button @click="tab = 'asesmen'" :class="tab === 'asesmen' ? 'bg-white shadow-sm text-[#3072ed]' : 'text-gray-500'" class="px-4 py-2 rounded-lg text-sm font-bold transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        Asesmen
                    </button>
                    {{-- [FITUR OPSI 1] Tab Game LKPD hanya muncul kalau guru mencentang opsi ini saat pembuatan modul --}}
                    @if ($module->include_game)
                        <button @click="tab = 'game'" :class="tab === 'game' ? 'bg-white shadow-sm text-[#3072ed]' : 'text-gray-500'" class="px-4 py-2 rounded-lg text-sm font-bold transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.065 7-9.542 7S3.732 16.057 2.458 12z"/></svg>
                            Game LKPD
                        </button>
                    @endif
                    {{-- [FITUR OPSI 2] Tab Media Ajar hanya muncul kalau guru mencentang opsi ini saat pembuatan modul --}}
                    @if ($module->include_flashcard)
                        <button @click="tab = 'media'" :class="tab === 'media' ? 'bg-white shadow-sm text-[#3072ed]' : 'text-gray-500'" class="px-4 py-2 rounded-lg text-sm font-bold transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M14 8h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Media Ajar
                        </button>
                    @endif
                </div>
                <a href="{{ route('modules.download', $module) }}" class="px-5 py-2.5 bg-[#2b454e] hover:bg-[#1a2e35] text-white text-sm font-bold rounded-xl shadow-md transition shrink-0 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"/></path></svg>
                    Export Learning Kit
                </a>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-[#f0f6ff] px-8 py-4 border-b border-blue-100 flex items-center justify-between">
                    <p class="font-bold text-[#2b454e] text-sm" x-text="{
                        modul: '📘 Modul Ajar / Lesson Plan',
                        lkpd: '📝 Lembar Kerja (LKPD)',
                        asesmen: '✅ Asesmen Formatif',
                    }[tab]"></p>
                    <span class="text-xs font-semibold text-[#3072ed] bg-white px-3 py-1 rounded-full border border-blue-100">Dihasilkan AI</span>
                </div>
                <div class="p-8 ai-output" x-show="tab !== 'game' && tab !== 'media'">
                    <div x-show="tab === 'modul'" x-html="modulAjar"></div>
                    <div x-show="tab === 'lkpd'" x-cloak x-html="lkpd"></div>
                    <div x-show="tab === 'asesmen'" x-cloak x-html="asesmen"></div>

                    {{-- [FIX BUG 1] Flashcard yang sudah dibuat guru ditampilkan sebagai
                         galeri pendukung di tab LKPD & Asesmen, memakai URL yang sudah
                         dinormalisasi (bukan path relatif mentah dari DB) supaya benar
                         benar tampil, bukan cuma teks alt. --}}
                    @if ($module->include_flashcard)
                        <template x-if="(tab === 'lkpd' || tab === 'asesmen') && media.length > 0">
                            <div class="mt-8 pt-6 border-t border-gray-100">
                                <p class="text-xs font-bold text-gray-500 uppercase mb-3">🖼️ Media Pendukung (Flashcard)</p>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                                    <template x-for="(item, index) in media" :key="index">
                                        <div class="border border-gray-100 rounded-2xl overflow-hidden">
                                            <img :src="item.url" :alt="item.label" class="w-full h-24 object-cover">
                                            <p class="text-xs font-bold text-center py-1.5 text-[#2b454e] capitalize" x-text="item.label"></p>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    @endif
                </div>

                {{-- Tab: Game LKPD Interaktif (opsional, lihat include_game) --}}
                @if ($module->include_game)
                <div x-show="tab === 'game'" x-cloak class="p-8">
                    <template x-if="!gameHtml">
                        <div class="text-center py-10">
                            <p class="text-sm text-gray-500 mb-4">Buat versi LKPD dalam bentuk game HTML interaktif berdasarkan Modul Ajar & LKPD yang sudah jadi.</p>
                            <button @click="generateGame()" :disabled="gameLoading" class="px-6 py-3 bg-[#3072ed] hover:bg-[#3162bf] text-white font-bold rounded-xl shadow-md transition disabled:opacity-50">
                                <span x-show="!gameLoading">🎮 Buat Game LKPD</span>
                                <span x-show="gameLoading">Menyusun game...</span>
                            </button>
                            <p x-show="gameError" x-text="gameError" class="text-red-500 text-xs mt-3"></p>
                        </div>
                    </template>
                    <template x-if="gameHtml">
                        <div>
                            <div class="flex justify-end gap-2 mb-3">
                                <a :href="`/modul/{{ $module->id }}/game/download`" class="px-4 py-2 bg-[#f0f6ff] text-[#3072ed] text-xs font-bold rounded-lg">Unduh Game (.html)</a>
                                <button @click="generateGame()" class="px-4 py-2 bg-gray-100 text-gray-600 text-xs font-bold rounded-lg">Buat Ulang</button>
                            </div>
                            <iframe :srcdoc="gameHtml" class="w-full h-[500px] border border-gray-200 rounded-2xl"></iframe>
                        </div>
                    </template>
                </div>
                @endif

                {{-- Tab: Media Ajar / Flashcard (AI Image Generation), opsional, lihat include_flashcard --}}
                @if ($module->include_flashcard)
                <div x-show="tab === 'media'" x-cloak class="p-8">
                    <div class="flex gap-2 mb-6">
                        <input x-model="flashcardLabel" type="text" placeholder="Contoh: apple, cat, book..."
                               class="flex-1 border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#3072ed]">
                        <button @click="generateImage()" :disabled="imgLoading || !flashcardLabel" class="px-5 py-2.5 bg-[#3072ed] hover:bg-[#3162bf] text-white text-sm font-bold rounded-xl disabled:opacity-50">
                            <span x-show="!imgLoading">🎨 Buat Flashcard</span>
                            <span x-show="imgLoading">Membuat gambar...</span>
                        </button>
                    </div>
                    <p x-show="imgError" x-text="imgError" class="text-red-500 text-xs mb-4"></p>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <template x-for="(item, index) in media" :key="index">
                            <div class="border border-gray-100 rounded-2xl overflow-hidden group relative">
                                <img :src="item.url" :alt="item.label" class="w-full h-28 object-cover">
                                <p class="text-xs font-bold text-center py-2 text-[#2b454e] capitalize" x-text="item.label"></p>
                                <button @click="deleteImage(index)" class="absolute top-1 right-1 bg-white/90 rounded-full p-1 opacity-0 group-hover:opacity-100 transition">
                                    <svg class="w-3.5 h-3.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
                @endif
            </div>
        </div>

    </div>

    <script>
        function learningKitProgress(moduleId, initialStatus) {
            return {
                moduleId,
                status: initialStatus,
                stepIndex: 0,
                errorMsg: '',
                modulAjar: @json($module->generated_content),
                lkpd: @json($module->generated_lkpd),
                asesmen: @json($module->generated_assessment),
                tips: @json($module->generated_tips),
                gameHtml: @json($module->generated_lkpd_game),
                gameLoading: false,
                gameError: '',
                // [FIX BUG 1] Pakai accessor generated_media_urls (selalu URL utuh
                // yang valid), BUKAN kolom generated_media mentah (path relatif
                // apa adanya dari DB) yang menyebabkan <img> gagal dimuat setelah
                // halaman di-refresh.
                media: @json($module->generated_media_urls),
                flashcardLabel: '',
                imgLoading: false,
                imgError: '',

                async generateGame() {
                    this.gameLoading = true;
                    this.gameError = '';
                    try {
                        const res = await fetch(`/modul/${this.moduleId}/game`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                        });
                        const data = await res.json();
                        if (!res.ok || !data.success) throw new Error(data.message || 'Gagal membuat game.');
                        window.location.reload(); // ambil ulang generated_lkpd_game dari DB
                    } catch (e) {
                        this.gameError = e.message;
                        this.gameLoading = false;
                    }
                },

                async generateImage() {
                    this.imgLoading = true;
                    this.imgError = '';
                    try {
                        const res = await fetch(`/modul/${this.moduleId}/image`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ label: this.flashcardLabel }),
                        });
                        const data = await res.json();
                        if (!res.ok || !data.success) throw new Error(data.message || 'Gagal membuat gambar.');
                        this.media.push({ label: data.label, url: data.url });
                        this.flashcardLabel = '';
                    } catch (e) {
                        this.imgError = e.message;
                    } finally {
                        this.imgLoading = false;
                    }
                },

                async deleteImage(index) {
                    await fetch(`/modul/${this.moduleId}/media/${index}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    });
                    this.media.splice(index, 1);
                },
                steps: [
                    { key: 'modul_ajar', label: 'Menyusun Modul Ajar (Lesson Plan)...' },
                    { key: 'lkpd', label: 'Menyusun Lembar Kerja Murid (LKPD)...' },
                    { key: 'asesmen', label: 'Menyusun Asesmen (Soal & Kunci Jawaban)...' },
                    { key: 'tips', label: 'Menyiapkan Rekomendasi Pedagogis AI...' },
                ],
                get currentLabel() {
                    return this.steps[this.stepIndex]?.label ?? 'Menyelesaikan...';
                },

                init() {
                    if (this.status === 'processing') {
                        this.runSteps();
                    }
                },

                async runSteps() {
                    for (let i = 0; i < this.steps.length; i++) {
                        this.stepIndex = i;
                        const ok = await this.callStep(this.steps[i].key);
                        if (!ok) return; // berhenti, status sudah diubah jadi 'error' di callStep()
                    }
                    this.stepIndex = this.steps.length;
                    this.status = 'completed';
                    // Ambil ulang isi modul yang baru selesai digenerate
                    await this.refreshContent();
                },

                async callStep(step) {
                    try {
                        const res = await fetch(`/modul/${this.moduleId}/step`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ step }),
                        });

                        const data = await res.json();

                        if (!res.ok || !data.success) {
                            this.status = 'error';
                            this.errorMsg = data.message || 'Terjadi kesalahan saat memproses.';
                            return false;
                        }

                        return true;
                    } catch (e) {
                        this.status = 'error';
                        this.errorMsg = 'Koneksi terputus saat menghubungi server.';
                        return false;
                    }
                },

                async refreshContent() {
                    // Reload halaman supaya Blade menyuntikkan isi terbaru dari DB ke JS.
                    window.location.reload();
                },

                retry() {
                    fetch(`/modul/${this.moduleId}/retry`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                    }).then(() => window.location.reload());
                },
            }
        }
    </script>
</x-classgate-layout>
