<?php

namespace App\Http\Controllers\Sekretaris;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\JenisSurat;
use Illuminate\Http\Request;

class JenisSuratController extends Controller
{
    public function index()
    {
        $jenisSurats = JenisSurat::withCount('surats')->orderBy('nama')->get();

        return view('sekretaris.jenis-surat.index', compact('jenisSurats'));
    }

    public function create()
    {
        return view('sekretaris.jenis-surat.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);
        $jenisSurat = JenisSurat::create($validated);

        ActivityLog::catat('create', $jenisSurat, "Menambahkan jenis surat {$jenisSurat->nama} ({$jenisSurat->kode})");

        return redirect()->route('sekretaris.jenis-surat.index')->with('success', 'Jenis surat berhasil ditambahkan.');
    }

    public function edit(JenisSurat $jenisSurat)
    {
        return view('sekretaris.jenis-surat.edit', compact('jenisSurat'));
    }

    public function update(Request $request, JenisSurat $jenisSurat)
    {
        $validated = $this->validated($request, $jenisSurat->id);
        $dataSebelum = $jenisSurat->only(array_keys($validated));

        // Mengubah kode TIDAK mempengaruhi nomor surat yang sudah pernah dibuat (nomor_surat disimpan sebagai string statis)
        $jenisSurat->update($validated);

        ActivityLog::catat('update', $jenisSurat, "Mengubah jenis surat {$jenisSurat->nama}", $dataSebelum, $jenisSurat->getChanges());

        return redirect()->route('sekretaris.jenis-surat.index')->with('success', 'Jenis surat berhasil diperbarui.');
    }

    public function destroy(JenisSurat $jenisSurat)
    {
        abort_if($jenisSurat->surats()->exists(), 422, 'Jenis surat ini sudah dipakai di arsip surat, tidak bisa dihapus.');

        $dataSebelum = $jenisSurat->toArray();
        $nama = $jenisSurat->nama;
        $jenisSurat->delete();

        ActivityLog::catat('delete', $jenisSurat, "Menghapus jenis surat {$nama}", $dataSebelum);

        return back()->with('success', 'Jenis surat berhasil dihapus.');
    }

    protected function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'nama' => 'required|string|max:100',
            'kode' => 'required|string|max:10|alpha_dash|unique:jenis_surats,kode'.($ignoreId ? ",{$ignoreId}" : ''),
        ]);
    }
}
