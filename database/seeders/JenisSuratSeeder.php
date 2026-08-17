<?php

namespace Database\Seeders;

use App\Models\JenisSurat;
use Illuminate\Database\Seeder;

class JenisSuratSeeder extends Seeder
{
    public function run(): void
    {
        $jenisSurats = [
            ['nama' => 'Surat Undangan', 'kode' => 'UND'],
            ['nama' => 'Surat Izin', 'kode' => 'SI'],
            ['nama' => 'Surat Keterangan', 'kode' => 'SKt'],
            ['nama' => 'Surat Pengantar', 'kode' => 'SP'],
            ['nama' => 'Surat Pemberitahuan', 'kode' => 'SPB'],
            ['nama' => 'Surat Permohonan', 'kode' => 'PMH'],
            ['nama' => 'Surat Tugas', 'kode' => 'ST'],
            ['nama' => 'Surat Keputusan', 'kode' => 'SKp'],
        ];

        foreach ($jenisSurats as $jenis) {
            JenisSurat::firstOrCreate(['kode' => $jenis['kode']], $jenis);
        }
    }
}
