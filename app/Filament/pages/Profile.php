<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Pages\Page;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;

class Profile extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static string $view = 'filament.pages.profile';
    protected static ?string $title = 'Profil Saya';

    public $name;
    public $email;
    public $phone;
    public $address;

    public array $passwordForm = [];

    public function mount(): void
    {
        $user = Auth::user();

        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->address = $user->address;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema($this->getProfileFormSchema())
            ->statePath('');
    }

    public function getPasswordForm(): Form
    {
        return $this->makeForm()
            ->schema($this->getPasswordFormSchema())
            ->statePath('passwordForm');
    }

    protected function getProfileFormSchema(): array
    {
        return [
            TextInput::make('name')
                ->label('Nama')
                ->required(),

            TextInput::make('email')
                ->label('Email')
                ->email()
                ->required(),

            TextInput::make('phone')
                ->label('No Telepon'),

            Textarea::make('address')
                ->label('Alamat'),
        ];
    }

    protected function getPasswordFormSchema(): array
    {
        return [
            TextInput::make('current_password')
                ->label('Password Saat Ini')
                ->password()
                ->required(),

            TextInput::make('new_password')
                ->label('Password Baru')
                ->password()
                ->required()
                ->minLength(6),

            TextInput::make('new_password_confirmation')
                ->label('Konfirmasi Password Baru')
                ->password()
                ->required()
                ->same('new_password'),
        ];
    }

    public function save(): void
    {
        $user = Auth::user();

        $user->update([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
        ]);

        $this->notify('success', 'Profil berhasil diperbarui.');
    }

    public function changePassword(): void
    {
        $user = Auth::user();

        if (! Hash::check($this->passwordForm['current_password'], $user->password)) {
            $this->addError('passwordForm.current_password', 'Password saat ini salah.');
            return;
        }

        $user->update([
            'password' => bcrypt($this->passwordForm['new_password']),
        ]);

        $this->reset('passwordForm');

        $this->notify('success', 'Password berhasil diubah.');
    }
}
