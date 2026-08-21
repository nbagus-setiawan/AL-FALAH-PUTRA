<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\KategoriRapb;
use App\Models\TahunAnggaran;
use Illuminate\Http\Request;

class KategoriRapbController extends Controller
{
    public function index(TahunAnggaran $tahunAnggaran)
    {
        $kategoris = $tahunAnggaran->kategoris()->withCount('subKategoris')->orderBy('jenis')->orderBy('nama')->get();

        return view('bendahara.kategori.index', compact('tahunAnggaran', 'kategoris'));
    }

    public function create(TahunAnggaran $tahunAnggaran)
    {
        return view('bendahara.kategori.create', compact('tahunAnggaran'));
    }

    public function store(Request $request, TahunAnggaran $tahunAnggaran)
    {
        // FIX: cegah penambahan kategori pada RAPB yang sudah disetujui Ketua Umum
        abort_if(
            $tahunAnggaran->isApprovalLocked(),
            422,
            'RAPB ini sudah disetujui Ketua Umum dan terkunci dari perubahan. Ajukan ulang terlebih dahulu jika ingin merevisi.'
        );

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'jenis' => 'required|in:Pemasukan,Pengeluaran',
        ]);

        $kategori = $tahunAnggaran->kategoris()->create($validated);

        ActivityLog::catat('create', $kategori, "Menambahkan kategori RAPB {$kategori->nama} ({$kategori->jenis}) di {$tahunAnggaran->nama}");

        // FIX: nama route shallow resource tetap memakai path penuh "tahun-anggaran.kategori.*"
        return redirect()
            ->route('bendahara.tahun-anggaran.kategori.index', $tahunAnggaran)
            ->with('success', 'Kategori RAPB berhasil ditambahkan.');
    }

    // Route shallow: show/edit/update/destroy tidak butuh tahunAnggaran di URL

    public function show(KategoriRapb $kategori)
    {
        $kategori->load('subKategoris.itemRincians.realisasis', 'tahunAnggaran');

        return view('bendahara.kategori.show', compact('kategori'));
    }

    public function edit(KategoriRapb $kategori)
    {
        return view('bendahara.kategori.edit', compact('kategori'));
    }

    public function update(Request $request, KategoriRapb $kategori)
    {
        // FIX: cegah edit kategori pada RAPB yang sudah disetujui Ketua Umum
        abort_if(
            $kategori->tahunAnggaran->isApprovalLocked(),
            422,
            'RAPB ini sudah disetujui Ketua Umum dan terkunci dari perubahan. Ajukan ulang terlebih dahulu jika ingin merevisi.'
        );

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'jenis' => 'required|in:Pemasukan,Pengeluaran',
        ]);

        $dataSebelum = $kategori->only(array_keys($validated));
        $kategori->update($validated);

        ActivityLog::catat('update', $kategori, "Mengubah kategori RAPB {$kategori->nama}", $dataSebelum, $kategori->getChanges());

        // Catatan: untuk action show/edit/update/destroy pada shallow resource,
        // nama route JUSTRU dipendekkan (bukan "tahun-anggaran.kategori.show").
        return redirect()->route('bendahara.kategori.show', $kategori)->with('success', 'Kategori RAPB diperbarui.');
    }

    public function destroy(KategoriRapb $kategori)
    {
        // FIX: cegah hapus kategori pada RAPB yang sudah disetujui Ketua Umum
        abort_if(
            $kategori->tahunAnggaran->isApprovalLocked(),
            422,
            'RAPB ini sudah disetujui Ketua Umum dan terkunci dari perubahan. Ajukan ulang terlebih dahulu jika ingin merevisi.'
        );

        abort_if($kategori->subKategoris()->exists(), 422, 'Kategori ini masih memiliki sub-kategori.');

        $dataSebelum = $kategori->toArray();
        $tahunAnggaran = $kategori->tahunAnggaran;
        $nama = $kategori->nama;
        $kategori->delete();

        ActivityLog::catat('delete', $kategori, "Menghapus kategori RAPB {$nama}", $dataSebelum);

        // FIX: nama route shallow resource tetap memakai path penuh "tahun-anggaran.kategori.*"
        return redirect()->route('bendahara.tahun-anggaran.kategori.index', $tahunAnggaran)->with('success', 'Kategori RAPB dihapus.');
    }
}