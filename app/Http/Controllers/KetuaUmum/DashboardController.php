<?php

namespace App\Http\Controllers\KetuaUmum;

use App\Http\Controllers\Controller;
use App\Models\Izin;
use App\Models\Pelanggaran;
use App\Models\Santri;
use App\Models\Surat;
use App\Models\TahunAnggaran;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $tahunAktif = TahunAnggaran::where('is_active', true)->first();

        return view('ketua-umum.dashboard', [
            'total_santri_aktif' => Santri::aktif()->count(),
            'santri_per_kelas' => Santri::aktif()
                ->select('kelas_id', DB::raw('count(*) as total'))
                ->groupBy('kelas_id')
                ->with('kelas:id,nama')
                ->get(),
            'sedang_izin' => Izin::where('status', Izin::STATUS_SEDANG_IZIN)->count(),
            'izin_pending' => Izin::where('status', Izin::STATUS_PENDING)->count(),
            'izin_terlambat' => Izin::where('status', Izin::STATUS_TERLAMBAT)->count(),

            'surat_masuk_bulan_ini' => Surat::where('arah', 'Masuk')
                ->whereMonth('tanggal', now()->month)->whereYear('tanggal', now()->year)->count(),
            'surat_keluar_bulan_ini' => Surat::where('arah', 'Keluar')
                ->whereMonth('tanggal', now()->month)->whereYear('tanggal', now()->year)->count(),

            'tahun_anggaran_aktif' => $tahunAktif,
            'rapb_rencana' => $tahunAktif?->totalRencana() ?? 0,
            'rapb_realisasi' => $tahunAktif?->totalRealisasi() ?? 0,

            // FIX: eager-load 'pelanggarans' & 'resetPoinHistory' supaya accessor
            // total_poin (dipakai untuk sort) tidak N+1 query per santri.
            'santri_poin_tertinggi' => Santri::aktif()->with(['pelanggarans', 'resetPoinHistory'])->get()
                ->sortByDesc(fn (Santri $s) => $s->total_poin)
                ->take(5)
                ->values(),
        ]);
    }
}