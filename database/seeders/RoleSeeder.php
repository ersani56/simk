<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
                // Buat role jika belum ada
                $adminRole = Role::firstOrCreate(['name' => 'admin']);
                $userRole = Role::firstOrCreate(['name' => 'user']);

                // Ambil user pertama dan jadikan admin
                $user = User::first();
                if ($user && !$user->hasRole('admin')) {
                    $user->assignRole('admin');
                }
    }
}
