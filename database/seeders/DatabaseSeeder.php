<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            JenisSuratSeeder::class,
            TingkatSeeder::class,
            UserSeeder::class,
        ]);
    }
}