<?php

namespace Database\Seeders;

use App\Models\Tingkat;
use Illuminate\Database\Seeder;

class TingkatSeeder extends Seeder
{
    /**
     * Jenjang default pondok pesantren. Sesuaikan nama/urutan di sini
     * kalau struktur jenjang pondok Bang berbeda (tidak perlu ubah migrasi).
     */
    public function run(): void
    {
        $tingkats = [
            ['nama' => 'Ula', 'urutan' => 1],
            ['nama' => 'Wustho', 'urutan' => 2],
            ['nama' => 'Ulya', 'urutan' => 3],
        ];

        foreach ($tingkats as $tingkat) {
            Tingkat::firstOrCreate(['urutan' => $tingkat['urutan']], $tingkat);
        }
    }
}
