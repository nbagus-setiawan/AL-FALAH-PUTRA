<?php

namespace App\Http\Controllers\Sekretaris;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Tingkat;
use Illuminate\Http\Request;

class TingkatController extends Controller
{
    public function index()
    {
        $tingkats = Tingkat::withCount('kelas')->orderBy('urutan')->get();

        return view('sekretaris.tingkat.index', compact('tingkats'));
    }

    public function create()
    {
        return view('sekretaris.tingkat.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);
        $tingkat = Tingkat::create($validated);

        ActivityLog::catat('create', $tingkat, "Menambahkan tingkat {$tingkat->nama}");

        return redirect()->route('sekretaris.tingkat.index')->with('success', 'Tingkat berhasil ditambahkan.');
    }

    public function edit(Tingkat $tingkat)
    {
        return view('sekretaris.tingkat.edit', compact('tingkat'));
    }

    public function update(Request $request, Tingkat $tingkat)
    {
        $validated = $this->validated($request, $tingkat->id);
        $dataSebelum = $tingkat->only(array_keys($validated));

        $tingkat->update($validated);

        ActivityLog::catat('update', $tingkat, "Mengubah tingkat {$tingkat->nama}", $dataSebelum, $tingkat->getChanges());

        return redirect()->route('sekretaris.tingkat.index')->with('success', 'Tingkat berhasil diperbarui.');
    }

    public function destroy(Tingkat $tingkat)
    {
        abort_if($tingkat->kelas()->exists(), 422, 'Tingkat ini masih memiliki kelas, hapus/pindahkan kelas terlebih dahulu.');

        $dataSebelum = $tingkat->toArray();
        $nama = $tingkat->nama;
        $tingkat->delete();

        ActivityLog::catat('delete', $tingkat, "Menghapus tingkat {$nama}", $dataSebelum);

        return back()->with('success', 'Tingkat berhasil dihapus.');
    }

    protected function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'nama' => 'required|string|max:100',
            'urutan' => 'required|integer|min:1|unique:tingkats,urutan'.($ignoreId ? ",{$ignoreId}" : ''),
        ]);
    }
}
