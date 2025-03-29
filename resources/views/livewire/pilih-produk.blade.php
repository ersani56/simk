<div class="grid grid-cols-3 gap-4">
    @foreach($products as $product)
        <div class="border rounded-lg p-4 cursor-pointer hover:bg-gray-100"
             wire:click="selectProduct('{{ $product->kode_bjadi }}', '{{ $product->nama_bjadi }}')">
            <img src="{{ $product->image_url }}" alt="{{ $product->nama_bjadi }}" class="w-full h-32 object-cover rounded">
            <p class="text-center mt-2 font-semibold">{{ $product->nama_bjadi }}</p>
        </div>
    @endforeach
</div>
