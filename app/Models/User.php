<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',       // tambahkan ini
        'address',     // dan ini
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Menentukan apakah user bisa mengakses Filament.
     */
    public function canAccessFilament(): bool
    {
        return $this->hasRole('admin','user'); // Hanya admin yang bisa akses Filament
    }

    /**
     * Menentukan apakah user bisa mengakses panel Filament.
     */
    public function canAccessPanel(\Filament\Panel $panel): bool
    {
        return true; // Bisa akses jika punya role 'admin' atau 'user'
    }
    public function pesanan()
    {
        return $this->hasMany(\App\Models\Pesanan::class, 'id'); // ganti 'user_id' kalau kamu pakai nama lain
    }

    public function kasbons()
    {
        return $this->hasMany(Kasbon::class);
    }

    public function gajiKaryawans()
    {
        return $this->hasMany(GajiKaryawan::class);
    }

}
