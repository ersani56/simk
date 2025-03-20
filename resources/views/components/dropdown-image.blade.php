<x-filament::dropdown>
    <x-filament::dropdown.trigger>
        <button class="flex items-center space-x-2 p-2 rounded-md bg-gray-100 hover:bg-gray-200">
            <span>Pilih Produk</span>
        </button>
    </x-filament::dropdown.trigger>

    <x-filament::dropdown.list>
        @foreach ($products as $product)
            <x-filament::dropdown.list.item image="{{ asset('storage/products/'.$product->image) }}">
                {{ $product->name }}
            </x-filament::dropdown.list.item>
        @endforeach
    </x-filament::dropdown.list>
</x-filament::dropdown>
