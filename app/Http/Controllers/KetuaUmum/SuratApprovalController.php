<?php

namespace App\Http\Controllers\KetuaUmum;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Surat;
use Illuminate\Http\Request;

class SuratApprovalController extends Controller
{
    public function index()
    {
        $suratPending = Surat::with(['jenisSurat', 'santri'])
            ->where('status_approval', Surat::APPROVAL_PENDING)
            ->latest('tanggal')
            ->paginate(20);

        return view('ketua-umum.surat.index', compact('suratPending'));
    }

    public function approve(Surat $surat)
    {
        abort_if($surat->status_approval !== Surat::APPROVAL_PENDING, 422, 'Surat ini sudah diproses sebelumnya.');

        $surat->update([
            'status_approval' => Surat::APPROVAL_APPROVED,
            'disetujui_oleh' => auth()->id(),
            'disetujui_pada' => now(),
        ]);

        ActivityLog::catat('approve', $surat, "Menyetujui surat {$surat->nomor_surat} — {$surat->perihal}");

        return back()->with('success', 'Surat disetujui.');
    }

    public function reject(Request $request, Surat $surat)
    {
        abort_if($surat->status_approval !== Surat::APPROVAL_PENDING, 422, 'Surat ini sudah diproses sebelumnya.');

        $validated = $request->validate([
            'catatan_penolakan' => 'required|string|max:500',
        ]);

        $surat->update([
            'status_approval' => Surat::APPROVAL_REJECTED,
            'disetujui_oleh' => auth()->id(),
            'disetujui_pada' => now(),
            'catatan_penolakan' => $validated['catatan_penolakan'],
        ]);

        ActivityLog::catat('reject', $surat, "Menolak surat {$surat->nomor_surat}: {$validated['catatan_penolakan']}");

        return back()->with('success', 'Surat ditolak.');
    }
}
