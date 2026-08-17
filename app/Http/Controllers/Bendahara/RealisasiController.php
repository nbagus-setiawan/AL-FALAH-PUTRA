<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ItemRincian;
use App\Models\Realisasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RealisasiController extends Controller
{
    public function store(Request $request, ItemRincian $itemRincian)
    {
        $validated = $request->validate([
            'jumlah' => 'required|numeric|min:0.01',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string',
            'bukti' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        // Bukti realisasi disimpan di storage privat, tidak bisa diakses langsung lewat URL publik
        $buktiPath = $request->file('bukti')->store('rapb/bukti', 'private');

        $realisasi = Realisasi::create([
            'item_rincian_id' => $itemRincian->id,
            'jumlah' => $validated['jumlah'],
            'tanggal' => $validated['tanggal'],
            'keterangan' => $validated['keterangan'] ?? null,
            'bukti_path' => $buktiPath,
            'dicatat_oleh' => auth()->id(),
        ]);

        ActivityLog::catat(
            'create',
            $realisasi,
            "Mencatat realisasi Rp".number_format($realisasi->jumlah, 0, ',', '.')." untuk item {$itemRincian->nama}"
        );

        return back()->with('success', 'Realisasi berhasil dicatat.');
    }

    public function destroy(Realisasi $realisasi)
    {
        $dataSebelum = $realisasi->toArray();
        $itemNama = $realisasi->itemRincian->nama;
        $realisasi->delete();

        ActivityLog::catat('delete', $realisasi, "Menghapus realisasi pada item {$itemNama}", $dataSebelum);

        return back()->with('success', 'Realisasi dihapus.');
    }

    /**
     * Stream bukti realisasi dari disk privat.
     * Route didaftarkan untuk Bendahara (yang menginput) dan Ketua Umum
     * (yang meninjau RAPB lewat RapbApprovalController) — keduanya perlu
     * membuka bukti tanpa file tersebut pernah ter-expose lewat URL publik.
     */
    public function bukti(Realisasi $realisasi)
    {
        abort_if(empty($realisasi->bukti_path), 404, 'Realisasi ini tidak memiliki bukti.');
        abort_unless(Storage::disk('private')->exists($realisasi->bukti_path), 404, 'File bukti tidak ditemukan.');

        return Storage::disk('private')->response(
            $realisasi->bukti_path,
            "bukti-realisasi-{$realisasi->id}.".pathinfo($realisasi->bukti_path, PATHINFO_EXTENSION)
        );
    }
}