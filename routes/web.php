<?php

use App\Http\Controllers\Bendahara\DashboardController as BendaharaDashboardController;
use App\Http\Controllers\Bendahara\ItemRincianController;
use App\Http\Controllers\Bendahara\KategoriRapbController;
use App\Http\Controllers\Bendahara\RealisasiController;
use App\Http\Controllers\Bendahara\SubKategoriRapbController;
use App\Http\Controllers\Bendahara\TahunAnggaranController;
use App\Http\Controllers\KetuaUmum\ActivityLogController;
use App\Http\Controllers\KetuaUmum\DashboardController as KetuaUmumDashboardController;
use App\Http\Controllers\KetuaUmum\IzinApprovalController;
use App\Http\Controllers\KetuaUmum\RapbApprovalController;
use App\Http\Controllers\KetuaUmum\SuratApprovalController;
use App\Http\Controllers\KetuaUmum\UserController as KetuaUmumUserController;
use App\Http\Controllers\Sekretaris\AsramaController;
use App\Http\Controllers\Sekretaris\DashboardController as SekretarisDashboardController;
use App\Http\Controllers\Sekretaris\IzinController;
use App\Http\Controllers\Sekretaris\JenisSuratController;
use App\Http\Controllers\Sekretaris\KelasController;
use App\Http\Controllers\Sekretaris\KenaikanKelasController;
use App\Http\Controllers\Sekretaris\PelanggaranController;
use App\Http\Controllers\Sekretaris\PengurusController;
use App\Http\Controllers\Sekretaris\SantriController;
use App\Http\Controllers\Sekretaris\SuratController;
use App\Http\Controllers\Sekretaris\TingkatController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — SIAP AFP
|--------------------------------------------------------------------------
| Semua route ada dalam satu file ini, dikelompokkan per role dengan
| middleware('role:...') + prefix + name, supaya tetap jelas siapa boleh
| akses apa tanpa perlu memecah ke banyak file.
|
| CATATAN PERBAIKAN (lihat juga PRD): sebelumnya beberapa controller sudah
| dibuat tapi belum pernah didaftarkan di sini — JenisSuratController,
| TingkatController, KetuaUmum\UserController, SubKategoriRapbController,
| IzinController::tandaiKembali()/generateSurat(), dan ActivityLogController
| — semua sudah ditambahkan di bawah. Route resource('item-rincian', ...)
| juga di-exclude dari 'index' karena controller-nya memang tidak punya
| method index() (item selalu diakses lewat halaman detail kategori).
| Route baru untuk streaming file privat (lampiran surat & bukti realisasi)
| juga ditambahkan supaya file yang sudah tersimpan di disk 'private' bisa
| benar-benar dibuka lewat browser oleh role yang berwenang.
*/

Route::get('/', fn () => redirect()->route('login'));

require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| KETUA UMUM
|--------------------------------------------------------------------------
| Akses penuh baca semua modul; approval tertinggi untuk Surat, Izin, RAPB;
| satu-satunya role yang boleh membuat/mengubah akun user (UserController).
| URL diawali /ketua-umum/..., nama route diawali "ketua-umum."
*/
Route::middleware(['auth', 'role:ketua_umum'])
    ->prefix('ketua-umum')
    ->name('ketua-umum.')
    ->group(function () {
        Route::get('/dashboard', [KetuaUmumDashboardController::class, 'index'])->name('dashboard');

        // Approval Izin
        Route::get('/izin', [IzinApprovalController::class, 'index'])->name('izin.index');
        Route::post('/izin/{izin}/approve', [IzinApprovalController::class, 'approve'])->name('izin.approve');
        Route::post('/izin/{izin}/reject', [IzinApprovalController::class, 'reject'])->name('izin.reject');

        // Approval Surat
        Route::get('/surat', [SuratApprovalController::class, 'index'])->name('surat.index');
        Route::post('/surat/{surat}/approve', [SuratApprovalController::class, 'approve'])->name('surat.approve');
        Route::post('/surat/{surat}/reject', [SuratApprovalController::class, 'reject'])->name('surat.reject');
        Route::get('/surat/{surat}/lampiran', [SuratController::class, 'lampiran'])->name('surat.lampiran');

        // Approval RAPB (read-only + approve/reject, lihat PRD 5.6)
        Route::get('/rapb', [RapbApprovalController::class, 'index'])->name('rapb.index');
        Route::get('/rapb/{tahunAnggaran}', [RapbApprovalController::class, 'show'])->name('rapb.show');
        Route::post('/rapb/{tahunAnggaran}/approve', [RapbApprovalController::class, 'approve'])->name('rapb.approve');
        Route::post('/rapb/{tahunAnggaran}/reject', [RapbApprovalController::class, 'reject'])->name('rapb.reject');
        Route::get('/realisasi/{realisasi}/bukti', [RealisasiController::class, 'bukti'])->name('realisasi.bukti');

        // Manajemen User — sebelumnya controller sudah lengkap tapi tidak ter-route
        Route::resource('user', KetuaUmumUserController::class)->except(['show']);

        // Log Aktivitas / audit trail — controller sudah ada, route sebelumnya belum didaftarkan
        Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');
        Route::get('/activity-log/{activityLog}', [ActivityLogController::class, 'show'])->name('activity-log.show');
    });

/*
|--------------------------------------------------------------------------
| SEKRETARIS
|--------------------------------------------------------------------------
| Kelola data master, arsip surat, generator surat, input izin & kedisiplinan.
| Satu-satunya role yang bisa akses data kesehatan & NIK santri.
| URL diawali /sekretaris/..., nama route diawali "sekretaris."
*/
Route::middleware(['auth', 'role:sekretaris'])
    ->prefix('sekretaris')
    ->name('sekretaris.')
    ->group(function () {
        Route::get('/dashboard', [SekretarisDashboardController::class, 'index'])->name('dashboard');

        // Master Data
        Route::resource('santri', SantriController::class);
        Route::resource('pengurus', PengurusController::class);
        Route::resource('kelas', KelasController::class);
        Route::resource('tingkat', TingkatController::class)->except(['show']);
        Route::resource('asrama', AsramaController::class);
        Route::post('/santri/{santri}/pindah-asrama', [AsramaController::class, 'pindahkanSantri'])->name('santri.pindah-asrama');

        // Kenaikan Kelas / Bulk Promosi
        Route::get('/kenaikan-kelas', [KenaikanKelasController::class, 'preview'])->name('kenaikan-kelas.preview');
        Route::post('/kenaikan-kelas/proses', [KenaikanKelasController::class, 'proses'])->name('kenaikan-kelas.proses');

        // Jenis Surat — master data untuk Generator Surat, controller sudah ada, route belum pernah ada
        Route::resource('jenis-surat', JenisSuratController::class)->except(['show']);

        // Arsip Surat + Generator Surat
        Route::resource('surat', SuratController::class);
        Route::get('/surat/{surat}/cetak', [SuratController::class, 'cetakPdf'])->name('surat.cetak');
        Route::get('/surat/{surat}/lampiran', [SuratController::class, 'lampiran'])->name('surat.lampiran');

        // Perizinan — input & lihat status (approve/reject tetap di Ketua Umum)
        Route::resource('izin', IzinController::class)->except(['edit', 'update']);
        Route::post('/izin/{izin}/tandai-kembali', [IzinController::class, 'tandaiKembali'])->name('izin.tandai-kembali');
        Route::post('/izin/{izin}/generate-surat', [IzinController::class, 'generateSurat'])->name('izin.generate-surat');

        // Poin Kedisiplinan
        Route::resource('pelanggaran', PelanggaranController::class);
        Route::post('/pelanggaran/santri/{santri}/reset-poin', [PelanggaranController::class, 'resetPoin'])->name('pelanggaran.reset-poin');
    });

/*
|--------------------------------------------------------------------------
| BENDAHARA
|--------------------------------------------------------------------------
| Kelola RAPB penuh (rencana, realisasi, kategori, cetak LPJ).
| Tidak punya akses ke modul lain di luar RAPB.
| URL diawali /bendahara/..., nama route diawali "bendahara."
*/
Route::middleware(['auth', 'role:bendahara'])
    ->prefix('bendahara')
    ->name('bendahara.')
    ->group(function () {
        Route::get('/dashboard', [BendaharaDashboardController::class, 'index'])->name('dashboard');

        Route::resource('tahun-anggaran', TahunAnggaranController::class);
        Route::post('/tahun-anggaran/{tahunAnggaran}/ajukan', [TahunAnggaranController::class, 'ajukanApproval'])->name('tahun-anggaran.ajukan');

        Route::resource('tahun-anggaran.kategori', KategoriRapbController::class)->shallow();

        // Sub-kategori RAPB — controller sudah ada, route sebelumnya belum pernah didaftarkan
        Route::post('/kategori/{kategori}/sub-kategori', [SubKategoriRapbController::class, 'store'])->name('sub-kategori.store');
        Route::put('/sub-kategori/{subKategori}', [SubKategoriRapbController::class, 'update'])->name('sub-kategori.update');
        Route::delete('/sub-kategori/{subKategori}', [SubKategoriRapbController::class, 'destroy'])->name('sub-kategori.destroy');

        // 'index' di-exclude karena ItemRincianController tidak punya method index()
        // (daftar item selalu diakses lewat halaman detail kategori: kategori.show)
        Route::resource('item-rincian', ItemRincianController::class)->except(['index']);

        Route::post('/item-rincian/{itemRincian}/realisasi', [RealisasiController::class, 'store'])->name('realisasi.store');
        Route::delete('/realisasi/{realisasi}', [RealisasiController::class, 'destroy'])->name('realisasi.destroy');
        Route::get('/realisasi/{realisasi}/bukti', [RealisasiController::class, 'bukti'])->name('realisasi.bukti');

        Route::get('/tahun-anggaran/{tahunAnggaran}/lpj', [TahunAnggaranController::class, 'cetakLpj'])->name('tahun-anggaran.lpj');
    });