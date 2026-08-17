<?php

use App\Http\Controllers\Bendahara\DashboardController as BendaharaDashboardController;
use App\Http\Controllers\Bendahara\ItemRincianController;
use App\Http\Controllers\Bendahara\KategoriRapbController;
use App\Http\Controllers\Bendahara\RealisasiController;
use App\Http\Controllers\Bendahara\TahunAnggaranController;
use App\Http\Controllers\KetuaUmum\DashboardController as KetuaUmumDashboardController;
use App\Http\Controllers\KetuaUmum\IzinApprovalController;
use App\Http\Controllers\KetuaUmum\RapbApprovalController;
use App\Http\Controllers\KetuaUmum\SuratApprovalController;
use App\Http\Controllers\Sekretaris\AsramaController;
use App\Http\Controllers\Sekretaris\DashboardController as SekretarisDashboardController;
use App\Http\Controllers\Sekretaris\IzinController;
use App\Http\Controllers\Sekretaris\KelasController;
use App\Http\Controllers\Sekretaris\KenaikanKelasController;
use App\Http\Controllers\Sekretaris\PelanggaranController;
use App\Http\Controllers\Sekretaris\PengurusController;
use App\Http\Controllers\Sekretaris\SantriController;
use App\Http\Controllers\Sekretaris\SuratController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — SIAP AFP
|--------------------------------------------------------------------------
| Semua route ada dalam satu file ini, dikelompokkan per role dengan
| middleware('role:...') + prefix + name, supaya tetap jelas siapa boleh
| akses apa tanpa perlu memecah ke banyak file.
*/

Route::get('/', fn () => redirect()->route('login'));

require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| KETUA UMUM
|--------------------------------------------------------------------------
| Akses penuh baca semua modul; approval tertinggi untuk Surat, Izin, RAPB.
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

        // Approval RAPB
        Route::get('/rapb', [RapbApprovalController::class, 'index'])->name('rapb.index');
        Route::get('/rapb/{tahunAnggaran}', [RapbApprovalController::class, 'show'])->name('rapb.show');
        Route::post('/rapb/{tahunAnggaran}/approve', [RapbApprovalController::class, 'approve'])->name('rapb.approve');
        Route::post('/rapb/{tahunAnggaran}/reject', [RapbApprovalController::class, 'reject'])->name('rapb.reject');

        // Ketua Umum juga punya akses baca-saja ke modul lain (Santri, Surat, Kedisiplinan)
        // — ditambahkan sesuai kebutuhan saat masing-masing modul dibangun.
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
        Route::resource('asrama', AsramaController::class);
        Route::post('/santri/{santri}/pindah-asrama', [AsramaController::class, 'pindahkanSantri'])->name('santri.pindah-asrama');

        // Kenaikan Kelas / Bulk Promosi
        Route::get('/kenaikan-kelas', [KenaikanKelasController::class, 'preview'])->name('kenaikan-kelas.preview');
        Route::post('/kenaikan-kelas/proses', [KenaikanKelasController::class, 'proses'])->name('kenaikan-kelas.proses');

        // Arsip Surat + Generator Surat
        Route::resource('surat', SuratController::class);
        Route::get('/surat/{surat}/cetak', [SuratController::class, 'cetakPdf'])->name('surat.cetak');

        // Perizinan — input & lihat status (approve/reject tetap di Ketua Umum)
        Route::resource('izin', IzinController::class)->except(['edit', 'update']);

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
        Route::resource('item-rincian', ItemRincianController::class);

        Route::post('/item-rincian/{itemRincian}/realisasi', [RealisasiController::class, 'store'])->name('realisasi.store');
        Route::delete('/realisasi/{realisasi}', [RealisasiController::class, 'destroy'])->name('realisasi.destroy');

        Route::get('/tahun-anggaran/{tahunAnggaran}/lpj', [TahunAnggaranController::class, 'cetakLpj'])->name('tahun-anggaran.lpj');
    });
