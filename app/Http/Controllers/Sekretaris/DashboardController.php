<?php

namespace App\Http\Controllers\Sekretaris;

use App\Http\Controllers\Controller;
use App\Models\Izin;
use App\Models\Santri;
use App\Models\Surat;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            'total_santri_aktif' => Santri::aktif()->count(),
            'santri_per_kelas' => Santri::aktif()
                ->selectRaw('kelas_id, count(*) as total')
                ->groupBy('kelas_id')
                ->with('kelas:id,nama')
                ->get(),
            'sedang_izin' => Izin::where('status', Izin::STATUS_SEDANG_IZIN)->count(),
            'surat_bulan_ini' => Surat::whereMonth('tanggal', now()->month)
                ->whereYear('tanggal', now()->year)
                ->count(),
        ];

        return view('sekretaris.dashboard', $data);
    }
}
