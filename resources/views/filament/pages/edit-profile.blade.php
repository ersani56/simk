<x-filament::page>
    <form wire:submit.prevent="updateProfile" class="space-y-6">
        {{ $this->form }}
        <x-filament::button type="submit">
            Simpan Perubahan
        </x-filament::button>
    </form>
</x-filament::page>
