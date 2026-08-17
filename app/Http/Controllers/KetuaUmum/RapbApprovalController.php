<?php

namespace App\Http\Controllers\KetuaUmum;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\TahunAnggaran;
use Illuminate\Http\Request;

class RapbApprovalController extends Controller
{
    public function index()
    {
        $tahunAnggarans = TahunAnggaran::orderByDesc('tanggal_mulai')->paginate(10);

        return view('ketua-umum.rapb.index', compact('tahunAnggarans'));
    }

    /**
     * Detail RAPB — read only bagi Ketua Umum (tanpa hak edit input, sesuai PRD 5.6).
     * Menampilkan rencana vs realisasi per kategori/sub-kategori/item.
     */
    public function show(TahunAnggaran $tahunAnggaran)
    {
        $tahunAnggaran->load('kategoris.subKategoris.itemRincians.realisasis');

        return view('ketua-umum.rapb.show', [
            'tahunAnggaran' => $tahunAnggaran,
            'totalRencana' => $tahunAnggaran->totalRencana(),
            'totalRealisasi' => $tahunAnggaran->totalRealisasi(),
        ]);
    }

    public function approve(TahunAnggaran $tahunAnggaran)
    {
        abort_if($tahunAnggaran->status_approval !== 'Pending', 422, 'RAPB ini sudah diproses sebelumnya.');

        $tahunAnggaran->update([
            'status_approval' => 'Approved',
            'disetujui_oleh' => auth()->id(),
            'disetujui_pada' => now(),
        ]);

        ActivityLog::catat('approve', $tahunAnggaran, "Menyetujui RAPB tahun anggaran {$tahunAnggaran->nama}");

        return back()->with('success', 'RAPB disetujui.');
    }

    public function reject(Request $request, TahunAnggaran $tahunAnggaran)
    {
        abort_if($tahunAnggaran->status_approval !== 'Pending', 422, 'RAPB ini sudah diproses sebelumnya.');

        $validated = $request->validate([
            'catatan_penolakan' => 'required|string|max:500',
        ]);

        $tahunAnggaran->update([
            'status_approval' => 'Rejected',
            'disetujui_oleh' => auth()->id(),
            'disetujui_pada' => now(),
            'catatan_penolakan' => $validated['catatan_penolakan'],
        ]);

        ActivityLog::catat('reject', $tahunAnggaran, "Menolak RAPB tahun anggaran {$tahunAnggaran->nama}: {$validated['catatan_penolakan']}");

        return back()->with('success', 'RAPB ditolak.');
    }
}
