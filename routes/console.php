<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled Tasks — SIAP AFP
|--------------------------------------------------------------------------
| Laravel 11+ otomatis membaca Schedule::command(...) yang didefinisikan di
| file ini (karena routes/console.php didaftarkan lewat parameter
| `commands:` di bootstrap/app.php) — tidak perlu withSchedule() tambahan.
|
| Yang WAJIB disiapkan di server: satu cron job yang menjalankan
| `php artisan schedule:run` setiap menit, contoh entri crontab:
|
|   * * * * * cd /path-ke-project && php artisan schedule:run >> /dev/null 2>&1
|
| Di cPanel/shared hosting, buat Cron Job dengan perintah yang sama lewat
| menu "Cron Jobs", jadwal "Once Per Minute".
*/

// Ubah status izin dari Approved -> Sedang Izin begitu tanggal_keluar tercapai.
// Dijalankan dini hari supaya status sudah update sebelum jam kerja mulai.
Schedule::command('izin:update-sedang-izin')
    ->dailyAt('00:05')
    ->withoutOverlapping();

// Deteksi santri yang telat kembali dari izin + kirim notifikasi email ke Sekretaris.
// Dijalankan pagi hari (setelah waktu rencana_kembali pada hari itu logisnya sudah lewat).
Schedule::command('izin:update-terlambat')
    ->dailyAt('06:00')
    ->withoutOverlapping();