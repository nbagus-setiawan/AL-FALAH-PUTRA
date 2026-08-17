<?php

namespace App\Http\Controllers\KetuaUmum;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Izin;
use Illuminate\Http\Request;

class IzinApprovalController extends Controller
{
    public function index()
    {
        $izinPending = Izin::with('santri')
            ->where('status', Izin::STATUS_PENDING)
            ->latest()
            ->paginate(20);

        return view('ketua-umum.izin.index', compact('izinPending'));
    }

    public function approve(Izin $izin)
    {
        abort_if($izin->status !== Izin::STATUS_PENDING, 422, 'Izin ini sudah diproses sebelumnya.');

        $izin->update([
            'status' => Izin::STATUS_APPROVED,
            'disetujui_oleh' => auth()->id(),
            'disetujui_pada' => now(),
        ]);

        ActivityLog::catat('approve', $izin, "Menyetujui izin santri {$izin->santri->nama_lengkap}");

        // Begitu Approved, status otomatis lanjut ke "Sedang Izin" saat tanggal_keluar tercapai
        // (bisa dijalankan via scheduled job/observer — lihat catatan di README)

        return back()->with('success', 'Pengajuan izin disetujui.');
    }

    public function reject(Request $request, Izin $izin)
    {
        abort_if($izin->status !== Izin::STATUS_PENDING, 422, 'Izin ini sudah diproses sebelumnya.');

        $validated = $request->validate([
            'catatan_penolakan' => 'required|string|max:500',
        ]);

        $izin->update([
            'status' => Izin::STATUS_REJECTED,
            'disetujui_oleh' => auth()->id(),
            'disetujui_pada' => now(),
            'catatan_penolakan' => $validated['catatan_penolakan'],
        ]);

        ActivityLog::catat('reject', $izin, "Menolak izin santri {$izin->santri->nama_lengkap}: {$validated['catatan_penolakan']}");

        return back()->with('success', 'Pengajuan izin ditolak.');
    }
}
