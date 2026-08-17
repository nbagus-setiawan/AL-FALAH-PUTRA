<?php

namespace App\Http\Controllers\Sekretaris;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Kelas;
use App\Models\Tingkat;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index()
    {
        $kelas = Kelas::with('tingkat')
            ->withCount('santris')
            ->orderBy('tahun_ajaran', 'desc')
            ->orderBy('nama')
            ->paginate(25);

        return view('sekretaris.kelas.index', compact('kelas'));
    }

    public function create()
    {
        return view('sekretaris.kelas.create', [
            'tingkatList' => Tingkat::orderBy('urutan')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);
        $kelas = Kelas::create($validated);

        ActivityLog::catat('create', $kelas, "Menambahkan kelas {$kelas->nama} ({$kelas->tahun_ajaran})");

        return redirect()->route('sekretaris.kelas.index')->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function show(Kelas $kelas)
    {
        $kelas->load('tingkat', 'santris');

        return view('sekretaris.kelas.show', compact('kelas'));
    }

    public function edit(Kelas $kelas)
    {
        return view('sekretaris.kelas.edit', [
            'kelas' => $kelas,
            'tingkatList' => Tingkat::orderBy('urutan')->get(),
        ]);
    }

    public function update(Request $request, Kelas $kelas)
    {
        $validated = $this->validated($request, $kelas->id);
        $dataSebelum = $kelas->only(array_keys($validated));

        $kelas->update($validated);

        ActivityLog::catat('update', $kelas, "Mengubah kelas {$kelas->nama}", $dataSebelum, $kelas->getChanges());

        return redirect()->route('sekretaris.kelas.index')->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroy(Kelas $kelas)
    {
        abort_if($kelas->santris()->exists(), 422, 'Kelas ini masih memiliki santri, pindahkan santri terlebih dahulu.');

        $dataSebelum = $kelas->toArray();
        $nama = $kelas->nama;
        $kelas->delete();

        ActivityLog::catat('delete', $kelas, "Menghapus kelas {$nama}", $dataSebelum);

        return back()->with('success', 'Kelas berhasil dihapus.');
    }

    protected function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'tingkat_id' => 'required|exists:tingkats,id',
            'nama' => 'required|string|max:100',
            'tahun_ajaran' => 'required|string|max:20',
        ]);
    }
}
