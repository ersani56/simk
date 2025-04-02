<div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4">
    <!-- Bagian Gambar Produk -->
    <div class="space-y-4">
        @if($product->gambar1)
            <div class="border rounded-lg overflow-hidden">
                <img src="{{ asset('storage/' . $product->gambar1) }}"
                     alt="Gambar Produk 1"
                     class="w-full h-auto object-cover">
                <p class="text-sm text-gray-500 mt-1">Gambar 1</p>
            </div>
        @endif

        @if($product->gambar2)
            <div class="border rounded-lg overflow-hidden">
                <img src="{{ asset('storage/' . $product->gambar2) }}"
                     alt="Gambar Produk 2"
                     class="w-full h-auto object-cover">
                <p class="text-sm text-gray-500 mt-1">Gambar 2</p>
            </div>
        @endif
    </div>

    <!-- Bagian Informasi Produk -->
    <div class="space-y-4">
        <div class="space-y-2">
            <h2 class="text-xl font-bold">{{ $product->nama_bjadi }}</h2>
            <div class="flex items-center space-x-2">
                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">
                    Kode: {{ $product->kode_bjadi }}
                </span>
            </div>
        </div>

        <div class="space-y-3">
            <div>
                <h3 class="font-medium text-gray-700">Kategori</h3>
                <p>{{ $product->kategori ?? '-' }}</p>
            </div>

            <div>
                <h3 class="font-medium text-gray-700">Satuan</h3>
                <p>{{ $product->satuan ?? '-' }}</p>
            </div>

            <div>
                <h3 class="font-medium text-gray-700">Keterangan Pesanan</h3>
                <p class="bg-gray-50 p-2 rounded">{{ $record->keterangan ?? 'Tidak ada keterangan' }}</p>
            </div>
        </div>
    </div>
</div>
