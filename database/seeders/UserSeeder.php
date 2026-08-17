<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Ganti password default ini segera setelah instalasi awal
        User::firstOrCreate(
            ['username' => 'ketua'],
            [
                'name' => 'Ketua Umum',
                'email' => 'ketua@afp.local',
                'password' => Hash::make('ubah-password-ini'),
                'role' => User::ROLE_KETUA_UMUM,
            ]
        );

        User::firstOrCreate(
            ['username' => 'sekretaris'],
            [
                'name' => 'Sekretaris',
                'email' => 'sekretaris@afp.local',
                'password' => Hash::make('ubah-password-ini'),
                'role' => User::ROLE_SEKRETARIS,
            ]
        );

        User::firstOrCreate(
            ['username' => 'bendahara'],
            [
                'name' => 'Bendahara',
                'email' => 'bendahara@afp.local',
                'password' => Hash::make('ubah-password-ini'),
                'role' => User::ROLE_BENDAHARA,
            ]
        );
    }
}
