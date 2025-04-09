<x-filament::page>
    <form wire:submit.prevent="save" class="space-y-6">
        <x-filament::card>
            <x-slot name="header">
                <h2 class="text-lg font-bold">Edit Profil</h2>
            </x-slot>

            {{ $this->form }}

            <x-slot name="footer">
                <x-filament::button type="submit">
                    Simpan Profil
                </x-filament::button>
            </x-slot>
        </x-filament::card>
    </form>

    <hr class="my-6 border-t border-gray-300" />

    <form wire:submit.prevent="changePassword" class="space-y-6">
        <x-filament::card>
            <x-slot name="header">
                <h2 class="text-lg font-bold">Ubah Password</h2>
            </x-slot>

            {{ $this->getPasswordForm() }}

            <x-slot name="footer">
                <x-filament::button type="submit" color="danger">
                    Ubah Password
                </x-filament::button>
            </x-slot>
        </x-filament::card>
    </form>
</x-filament::page>
