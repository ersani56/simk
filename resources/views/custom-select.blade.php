@props(['options', 'selected'])

<div x-data="{ open: false, selected: @js($selected) }" class="relative">
    <button @click="open = !open" class="w-full p-2 border rounded-md bg-white flex items-center">
        <img x-show="selected" :src="'/storage/products/' + selected" class="w-8 h-8 rounded-full mr-2">
        <span x-text="selected || 'Pilih produk'"></span>
    </button>
    <div x-show="open" class="absolute bg-white border rounded-md shadow-md w-full mt-2 z-10">
        @foreach ($options as $key => $value)
            <div @click="selected = '{{ $value }}'; open = false;" class="cursor-pointer p-2 hover:bg-gray-200 flex items-center">
                <img src="{{ asset('storage/products/'.$value) }}" class="w-8 h-8 rounded-full mr-2">
                <span>{{ $key }}</span>
            </div>
        @endforeach
    </div>
</div>
