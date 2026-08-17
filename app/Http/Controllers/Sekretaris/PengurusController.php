<?php

namespace App\Http\Controllers\Sekretaris;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Pengurus;
use Illuminate\Http\Request;

class PengurusController extends Controller
{
    public function index(Request $request)
    {
        $pengurus = Pengurus::when($request->q, fn ($q) => $q->where('nama', 'like', "%{$request->q}%"))
            ->orderBy('nama')
            ->paginate(25);

        return view('sekretaris.pengurus.index', compact('pengurus'));
    }

    public function create()
    {
        return view('sekretaris.pengurus.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);
        $pengurus = Pengurus::create($validated);

        ActivityLog::catat('create', $pengurus, "Menambahkan data pengurus {$pengurus->nama}");

        return redirect()->route('sekretaris.pengurus.index')->with('success', 'Data pengurus berhasil ditambahkan.');
    }

    public function show(Pengurus $pengurus)
    {
        $pengurus->load('user', 'asramaDiawasi');

        return view('sekretaris.pengurus.show', compact('pengurus'));
    }

    public function edit(Pengurus $pengurus)
    {
        return view('sekretaris.pengurus.edit', compact('pengurus'));
    }

    public function update(Request $request, Pengurus $pengurus)
    {
        $validated = $this->validated($request, $pengurus->id);
        $dataSebelum = $pengurus->only(array_keys($validated));

        $pengurus->update($validated);

        ActivityLog::catat('update', $pengurus, "Mengubah data pengurus {$pengurus->nama}", $dataSebelum, $pengurus->getChanges());

        return redirect()->route('sekretaris.pengurus.index')->with('success', 'Data pengurus berhasil diperbarui.');
    }

    public function destroy(Pengurus $pengurus)
    {
        abort_if($pengurus->user()->exists(), 422, 'Pengurus ini masih terhubung ke akun user, hapus/lepas akun terlebih dahulu.');

        $dataSebelum = $pengurus->toArray();
        $nama = $pengurus->nama;
        $pengurus->delete();

        ActivityLog::catat('delete', $pengurus, "Menghapus data pengurus {$nama}", $dataSebelum);

        return back()->with('success', 'Data pengurus berhasil dihapus.');
    }

    protected function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'nama' => 'required|string|max:255',
            'nip_internal' => 'nullable|string|max:50|unique:pengurus,nip_internal'.($ignoreId ? ",{$ignoreId}" : ''),
            'jabatan' => 'required|string|max:100',
            'periode' => 'required|string|max:20',
            'kontak' => 'nullable|string|max:30',
            'is_active' => 'boolean',
        ]);
    }
}
