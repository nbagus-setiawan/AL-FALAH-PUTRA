<?php

namespace App\Console\Commands;

use App\Mail\IzinTerlambatNotification;
use App\Models\Izin;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Throwable;

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

        $jumlahGagalNotifikasi = 0;

        foreach ($izinTerlambat as $izin) {
            // Idempotent: aman dipanggil berkali-kali walau izin ini sudah pernah
            // ditandai Terlambat sebelumnya (mis. pada percobaan retry notifikasi).
            $izin->update(['status' => Izin::STATUS_TERLAMBAT]);

            if (! empty($emailSekretaris)) {
                // FIX: bungkus pengiriman email dengan try/catch. Sebelumnya, kalau
                // Mail::send() melempar exception (mis. SMTP down), seluruh command
                // langsung berhenti — santri-santri lain dalam antrean hari itu ikut
                // tidak diproses. Sekarang satu kegagalan hanya dicatat & dilewati,
                // proses lanjut ke izin berikutnya. Karena scopeTerlambatBelumNotif()
                // sudah diperluas untuk turut menyertakan status 'Terlambat' yang
                // notifikasi_telat_terkirim-nya masih false, izin ini akan otomatis
                // dicoba lagi pada jadwal berikutnya (self-healing retry).
                try {
                    Mail::to($emailSekretaris)->send(new IzinTerlambatNotification($izin));
                    $izin->update(['notifikasi_telat_terkirim' => true]);
                } catch (Throwable $e) {
                    $jumlahGagalNotifikasi++;
                    $this->error("Gagal mengirim notifikasi untuk izin #{$izin->id} ({$izin->santri->nama_lengkap}): {$e->getMessage()}");
                    report($e);

                    continue;
                }
            }

            $this->line("Izin #{$izin->id} — {$izin->santri->nama_lengkap} ditandai Terlambat, notifikasi dikirim.");
        }

        $ringkasan = "Selesai. {$izinTerlambat->count()} izin diproses.";
        if ($jumlahGagalNotifikasi > 0) {
            $ringkasan .= " {$jumlahGagalNotifikasi} notifikasi gagal terkirim (akan dicoba lagi otomatis pada jadwal berikutnya, lihat log untuk detail).";
        }
        $this->info($ringkasan);

        return self::SUCCESS;
    }
}