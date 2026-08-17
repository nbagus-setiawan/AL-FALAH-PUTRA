<?php

namespace App\Console\Commands;

use App\Mail\IzinTerlambatNotification;
use App\Models\Izin;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class UpdateStatusIzinTerlambat extends Command
{
    /**
     * Dijalankan terjadwal (lihat routes/console.php) — idealnya tiap hari pagi via cron cPanel.
     */
    protected $signature = 'izin:update-terlambat';

    protected $description = 'Update status izin yang sudah lewat rencana_kembali menjadi Terlambat, dan kirim notifikasi email ke Sekretaris';

    public function handle(): int
    {
        $izinTerlambat = Izin::terlambatBelumNotif()->with('santri')->get();

        if ($izinTerlambat->isEmpty()) {
            $this->info('Tidak ada izin yang terlambat.');

            return self::SUCCESS;
        }

        $emailSekretaris = User::where('role', User::ROLE_SEKRETARIS)
            ->where('is_active', true)
            ->pluck('email')
            ->filter()
            ->all();

        foreach ($izinTerlambat as $izin) {
            $izin->update(['status' => Izin::STATUS_TERLAMBAT]);

            if (! empty($emailSekretaris)) {
                // SMTP pondok sendiri via Laravel Mail — konfigurasi di .env (MAIL_HOST dst), bukan pihak ketiga
                Mail::to($emailSekretaris)->send(new IzinTerlambatNotification($izin));
                $izin->update(['notifikasi_telat_terkirim' => true]);
            }

            $this->line("Izin #{$izin->id} — {$izin->santri->nama_lengkap} ditandai Terlambat, notifikasi dikirim.");
        }

        $this->info("Selesai. {$izinTerlambat->count()} izin diproses.");

        return self::SUCCESS;
    }
}
