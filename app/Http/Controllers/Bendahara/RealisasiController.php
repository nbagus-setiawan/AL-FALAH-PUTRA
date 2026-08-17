<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ItemRincian;
use App\Models\Realisasi;
use Illuminate\Http\Request;

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
}
