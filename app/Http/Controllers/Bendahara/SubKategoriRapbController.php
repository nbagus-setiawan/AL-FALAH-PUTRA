<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\KategoriRapb;
use App\Models\SubKategoriRapb;
use Illuminate\Http\Request;

class SubKategoriRapbController extends Controller
{
    public function store(Request $request, KategoriRapb $kategori)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        $subKategori = $kategori->subKategoris()->create($validated);

        ActivityLog::catat('create', $subKategori, "Menambahkan sub-kategori {$subKategori->nama} di kategori {$kategori->nama}");

        return back()->with('success', 'Sub-kategori berhasil ditambahkan.');
    }

    public function update(Request $request, SubKategoriRapb $subKategori)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        $dataSebelum = $subKategori->only(array_keys($validated));
        $subKategori->update($validated);

        ActivityLog::catat('update', $subKategori, "Mengubah sub-kategori {$subKategori->nama}", $dataSebelum, $subKategori->getChanges());

        return back()->with('success', 'Sub-kategori diperbarui.');
    }

    public function destroy(SubKategoriRapb $subKategori)
    {
        abort_if($subKategori->itemRincians()->exists(), 422, 'Sub-kategori ini masih memiliki item rincian.');

        $dataSebelum = $subKategori->toArray();
        $nama = $subKategori->nama;
        $subKategori->delete();

        ActivityLog::catat('delete', $subKategori, "Menghapus sub-kategori {$nama}", $dataSebelum);

        return back()->with('success', 'Sub-kategori dihapus.');
    }
}
