<?php

namespace App\Console\Commands;

use App\Models\Izin;
use Illuminate\Console\Command;

class UpdateStatusIzinSedangIzin extends Command
{
    protected $signature = 'izin:update-sedang-izin';

    protected $description = 'Ubah status izin dari Approved ke Sedang Izin begitu tanggal_keluar tercapai';

    public function handle(): int
    {
        $jumlah = Izin::where('status', Izin::STATUS_APPROVED)
            ->whereDate('tanggal_keluar', '<=', now()->toDateString())
            ->update(['status' => Izin::STATUS_SEDANG_IZIN]);

        $this->info("{$jumlah} izin diubah menjadi status Sedang Izin.");

        return self::SUCCESS;
    }
}
