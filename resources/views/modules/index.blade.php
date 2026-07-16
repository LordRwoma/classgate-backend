<div class="max-w-6xl mx-auto py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Arsip Modul Ajar</h1>
            <p class="text-gray-500">Daftar semua modul yang pernah kamu susun.</p>
        </div>
        <a href="{{ route('modules.create') }}" class="px-6 py-3 bg-blue-600 text-white rounded-xl shadow-lg hover:bg-blue-700 transition">
            + Buat Modul Baru
        </a>
    </div>

    <div class="mb-6">
        <input type="text" placeholder="Cari berdasarkan topik atau fase..." class="w-full p-4 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500">
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($modules as $module)
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition">
            <div class="flex justify-between items-start mb-4">
                <span class="px-3 py-1 bg-blue-50 text-blue-600 text-xs font-semibold rounded-full uppercase">{{ $module->fase }}</span>
                <span class="text-gray-400 text-sm">{{ $module->created_at->format('d M Y') }}</span>
            </div>
            <h3 class="text-lg font-bold text-gray-800 mb-2">{{ $module->topik }}</h3>
            <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $module->deskripsi }}</p>
            
            <div class="flex gap-2">
                <a href="{{ route('modules.show', $module->id) }}" class="flex-1 text-center py-2 border border-gray-200 rounded-lg text-sm hover:bg-gray-50">Lihat</a>
                <a href="#" class="px-3 py-2 bg-gray-100 rounded-lg text-sm hover:bg-gray-200">
                    <i class="fas fa-download"></i> PDF
                </a>
            </div>
        </div>
        @endforeach
    </div>
</div>