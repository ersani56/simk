<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Filament\Notifications\Notification;

class Profile extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $title = 'Profil Saya';
    protected static string $view = 'filament.pages.profile';

    public ?array $data = [];

    public function mount(): void
    {
        $user = Auth::user();
        $this->form->fill([
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'address' => $user->address,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->label('Nama')->required(),
                TextInput::make('email')->label('Email')->email()->required(),
                TextInput::make('phone')->label('No Telepon'),
                TextInput::make('address')->label('Alamat')->columnSpanFull(),

                TextInput::make('current_password')
                    ->label('Password Saat Ini')
                    ->password()
                    ->columnSpanFull(),

                TextInput::make('new_password')
                    ->label('Password Baru')
                    ->password()
                    ->columnSpanFull(),

                TextInput::make('new_password_confirmation')
                    ->label('Konfirmasi Password Baru')
                    ->password()
                    ->same('new_password')
                    ->columnSpanFull(),
            ])
            ->statePath('data')
            ->columns(2);
    }

    public function save(): void
    {
        $user = Auth::user();
        $data = $this->form->getState();

        // Update profil
        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'address' => $data['address'],
        ]);

        // Cek apakah user ingin ubah password
        if ($data['current_password'] && $data['new_password']) {
            if (! Hash::check($data['current_password'], $user->password)) {
                Notification::make()
                    ->title('Password saat ini salah.')
                    ->danger()
                    ->send();
                return;
            }

            $user->update([
                'password' => bcrypt($data['new_password']),
            ]);
        }

        Notification::make()
            ->title('Profil berhasil diperbarui.')
            ->success()
            ->send();
    }
}
