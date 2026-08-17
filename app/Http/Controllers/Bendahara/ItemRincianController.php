<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ItemRincian;
use App\Models\SubKategoriRapb;
use Illuminate\Http\Request;

class ItemRincianController extends Controller
{
    public function create(Request $request)
    {
        return view('bendahara.item-rincian.create', [
            'subKategori' => SubKategoriRapb::findOrFail($request->sub_kategori_rapb_id),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sub_kategori_rapb_id' => 'required|exists:sub_kategori_rapbs,id',
            'nama' => 'required|string|max:255',
            'jumlah_rencana' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
            // Rencana bulanan opsional — jika diisi, jumlah_rencana otomatis dihitung ulang dari total per bulan
            'rencana_bulanan' => 'nullable|array',
            'rencana_bulanan.*' => 'nullable|numeric|min:0',
        ]);

        $rencanaBulanan = $validated['rencana_bulanan'] ?? null;
        unset($validated['rencana_bulanan']);

        if ($rencanaBulanan) {
            $validated['jumlah_rencana'] = array_sum($rencanaBulanan);
        }

        $item = ItemRincian::create($validated);

        if ($rencanaBulanan) {
            foreach ($rencanaBulanan as $bulan => $jumlah) {
                if ($jumlah > 0) {
                    $item->rencanaBulanans()->create(['bulan' => $bulan, 'jumlah' => $jumlah]);
                }
            }
        }

        ActivityLog::catat('create', $item, "Menambahkan item rincian {$item->nama} (Rp ".number_format($item->jumlah_rencana, 0, ',', '.').')');

        return redirect()
            ->route('bendahara.kategori.show', $item->subKategori->kategori_rapb_id)
            ->with('success', 'Item rincian berhasil ditambahkan.');
    }

    public function show(ItemRincian $itemRincian)
    {
        $itemRincian->load('rencanaBulanans', 'realisasis.pencatat', 'subKategori.kategori');

        return view('bendahara.item-rincian.show', compact('itemRincian'));
    }

    public function edit(ItemRincian $itemRincian)
    {
        $itemRincian->load('rencanaBulanans');

        return view('bendahara.item-rincian.edit', compact('itemRincian'));
    }

    public function update(Request $request, ItemRincian $itemRincian)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'jumlah_rencana' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
            'rencana_bulanan' => 'nullable|array',
            'rencana_bulanan.*' => 'nullable|numeric|min:0',
        ]);

        $rencanaBulanan = $validated['rencana_bulanan'] ?? null;
        unset($validated['rencana_bulanan']);

        if ($rencanaBulanan) {
            $validated['jumlah_rencana'] = array_sum($rencanaBulanan);
        }

        $dataSebelum = $itemRincian->only(array_keys($validated));
        $itemRincian->update($validated);

        if ($rencanaBulanan) {
            $itemRincian->rencanaBulanans()->delete();
            foreach ($rencanaBulanan as $bulan => $jumlah) {
                if ($jumlah > 0) {
                    $itemRincian->rencanaBulanans()->create(['bulan' => $bulan, 'jumlah' => $jumlah]);
                }
            }
        }

        ActivityLog::catat('update', $itemRincian, "Mengubah item rincian {$itemRincian->nama}", $dataSebelum, $itemRincian->getChanges());

        return redirect()->route('bendahara.item-rincian.show', $itemRincian)->with('success', 'Item rincian diperbarui.');
    }

    public function destroy(ItemRincian $itemRincian)
    {
        abort_if($itemRincian->realisasis()->exists(), 422, 'Item ini sudah memiliki realisasi, tidak bisa dihapus.');

        $dataSebelum = $itemRincian->toArray();
        $subKategoriId = $itemRincian->sub_kategori_rapb_id;
        $nama = $itemRincian->nama;
        $itemRincian->delete();

        ActivityLog::catat('delete', $itemRincian, "Menghapus item rincian {$nama}", $dataSebelum);

        return back()->with('success', 'Item rincian dihapus.');
    }
}
