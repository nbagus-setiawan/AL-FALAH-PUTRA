<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Models\TahunAnggaran;

class DashboardController extends Controller
{
    public function index()
    {
        $tahunAktif = TahunAnggaran::where('is_active', true)->first();

        $tahunAktif?->load('kategoris.subKategoris.itemRincians');

        return view('bendahara.dashboard', [
            'tahunAktif' => $tahunAktif,
            'totalRencana' => $tahunAktif?->totalRencana() ?? 0,
            'totalRealisasi' => $tahunAktif?->totalRealisasi() ?? 0,
            'riwayatTahunAnggaran' => TahunAnggaran::orderByDesc('tanggal_mulai')->take(5)->get(),
        ]);
    }
}
