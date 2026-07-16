<x-classgate-layout title="Arsip Saya" active="arsip">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <div class="flex flex-wrap justify-between items-center gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-[#2b454e] mb-1">Arsip Saya</h1>
                <p class="text-gray-500">Kelola seluruh perangkat ajar yang pernah kamu buat.</p>
            </div>
            <div class="bg-[#f0f6ff] text-[#3072ed] font-bold px-4 py-2 rounded-xl border border-blue-100">
                {{ $modules->total() }} Modul
            </div>
        </div>

        <form method="GET" action="{{ route('modules.history') }}" class="mb-6">
            <div class="relative max-w-md">
                <svg class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari berdasarkan topik..."
                       class="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#3072ed]">
            </div>
        </form>

        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
            @if ($modules->isEmpty())
                <div class="text-center py-16 text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <p class="mb-4">{{ request('search') ? 'Tidak ada modul yang cocok dengan pencarian.' : 'Belum ada modul tersimpan.' }}</p>
                    <a href="{{ route('modules.create') }}" class="inline-block px-6 py-3 bg-[#3072ed] text-white font-bold rounded-xl">+ Buat Modul Pertama</a>
                </div>
            @else
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-xs font-bold text-gray-500 uppercase">
                            <th class="p-4 pl-6">Topik</th>
                            <th class="p-4">Fase & Kelas</th>
                            <th class="p-4">Alokasi Waktu</th>
                            <th class="p-4">Dibuat</th>
                            <th class="p-4">Status</th>
                            <th class="p-4 text-right pr-6">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @foreach ($modules as $item)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="p-4 pl-6">
                                    <a href="{{ route('modules.show', $item) }}" class="font-bold text-[#2b454e] hover:text-[#3072ed]">{{ $item->topic }}</a>
                                    <p class="text-xs text-gray-400">{{ $item->subject }}</p>
                                </td>
                                <td class="p-4 font-medium">{{ $item->phase }}</td>
                                <td class="p-4 text-gray-500">{{ $item->duration }}</td>
                                <td class="p-4 text-gray-500">{{ $item->created_at->translatedFormat('d M Y') }}</td>
                                <td class="p-4">
                                    <span class="text-xs font-semibold px-3 py-1 rounded-full
                                        {{ match($item->status) {
                                            'processing' => 'bg-yellow-100 text-yellow-700',
                                            'completed' => 'bg-green-100 text-green-700',
                                            'error' => 'bg-red-100 text-red-700',
                                            default => 'bg-gray-100 text-gray-600',
                                        } }}">
                                        {{ match($item->status) {
                                            'processing' => 'Diproses',
                                            'completed' => 'Selesai',
                                            'error' => 'Gagal',
                                            default => $item->status,
                                        } }}
                                    </span>
                                </td>
                                <td class="p-4 pr-6">
                                    <div class="flex items-center justify-end gap-3">
                                        @if ($item->status === 'completed')
                                            <a href="{{ route('modules.download', $item) }}" title="Download" class="text-gray-400 hover:text-[#3072ed] transition">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"/></path></svg>
                                            </a>
                                        @endif
                                        <form method="POST" action="{{ route('modules.destroy', $item) }}" onsubmit="return confirm('Hapus modul ini secara permanen?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Hapus" class="text-gray-400 hover:text-red-500 transition">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="p-4">{{ $modules->appends(request()->query())->links() }}</div>
            @endif
        </div>
    </div>
</x-classgate-layout>
