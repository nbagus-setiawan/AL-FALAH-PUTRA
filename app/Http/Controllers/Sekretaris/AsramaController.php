<?php

namespace App\Http\Controllers\Sekretaris;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Asrama;
use App\Models\AsramaPenghuni;
use App\Models\Pengurus;
use App\Models\Santri;
use Illuminate\Http\Request;

class AsramaController extends Controller
{
    public function index()
    {
        $asramas = Asrama::with('penanggungJawab')->withCount('penghuniAktif')->orderBy('nama')->get();

        return view('sekretaris.asrama.index', compact('asramas'));
    }

    public function create()
    {
        return view('sekretaris.asrama.create', [
            'pengurusList' => Pengurus::where('is_active', true)->orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);
        $asrama = Asrama::create($validated);

        ActivityLog::catat('create', $asrama, "Menambahkan asrama {$asrama->nama}");

        return redirect()->route('sekretaris.asrama.index')->with('success', 'Asrama berhasil ditambahkan.');
    }

    public function show(Asrama $asrama)
    {
        $asrama->load('penanggungJawab', 'penghuniAktif.santri', 'riwayatPenghuni.santri');

        return view('sekretaris.asrama.show', compact('asrama'));
    }

    public function edit(Asrama $asrama)
    {
        return view('sekretaris.asrama.edit', [
            'asrama' => $asrama,
            'pengurusList' => Pengurus::where('is_active', true)->orderBy('nama')->get(),
        ]);
    }

    public function update(Request $request, Asrama $asrama)
    {
        $validated = $this->validated($request, $asrama->id);
        $dataSebelum = $asrama->only(array_keys($validated));

        $asrama->update($validated);

        ActivityLog::catat('update', $asrama, "Mengubah data asrama {$asrama->nama}", $dataSebelum, $asrama->getChanges());

        return redirect()->route('sekretaris.asrama.index')->with('success', 'Asrama berhasil diperbarui.');
    }

    public function destroy(Asrama $asrama)
    {
        abort_if($asrama->jumlahPenghuniAktif() > 0, 422, 'Asrama ini masih memiliki penghuni aktif.');

        $dataSebelum = $asrama->toArray();
        $nama = $asrama->nama;
        $asrama->delete();

        ActivityLog::catat('delete', $asrama, "Menghapus asrama {$nama}", $dataSebelum);

        return back()->with('success', 'Asrama berhasil dihapus.');
    }

    /**
     * Pindahkan santri ke asrama lain. Riwayat penghuni lama TIDAK ditimpa
     * — ditutup dengan tanggal_keluar, lalu baris baru dibuat (lihat AsramaPenghuni::pindahkan()).
     */
    public function pindahkanSantri(Request $request, Santri $santri)
    {
        $validated = $request->validate([
            'asrama_id' => 'required|exists:asramas,id',
            'tanggal' => 'nullable|date',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $asramaBaru = Asrama::findOrFail($validated['asrama_id']);

        abort_if(
            $asramaBaru->jumlahPenghuniAktif() >= $asramaBaru->kapasitas,
            422,
            'Asrama tujuan sudah mencapai kapasitas maksimum.'
        );

        $penghuniBaru = AsramaPenghuni::pindahkan($santri, $asramaBaru, $validated['tanggal'] ?? null, $validated['keterangan'] ?? null);

        ActivityLog::catat(
            'pindah_asrama',
            $santri,
            "Memindahkan santri {$santri->nama_lengkap} ke asrama {$asramaBaru->nama}"
        );

        return back()->with('success', "Santri berhasil dipindahkan ke asrama {$asramaBaru->nama}.");
    }

    protected function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'nama' => 'required|string|max:100',
            'kapasitas' => 'required|integer|min:1',
            'penanggung_jawab_id' => 'nullable|exists:pengurus,id',
        ]);
    }
}
