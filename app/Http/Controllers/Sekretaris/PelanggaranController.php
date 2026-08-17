<?php

namespace App\Http\Controllers\Sekretaris;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Pelanggaran;
use App\Models\ResetPoinSantri;
use App\Models\Santri;
use Illuminate\Http\Request;

class PelanggaranController extends Controller
{
    public function index(Request $request)
    {
        $pelanggarans = Pelanggaran::with('santri')
            ->when($request->santri_id, fn ($q) => $q->where('santri_id', $request->santri_id))
            ->when($request->kategori, fn ($q) => $q->where('kategori', $request->kategori))
            ->latest('tanggal')
            ->paginate(25);

        // Laporan santri dengan poin tertinggi — dihitung di memori karena total_poin adalah accessor (mempertimbangkan reset)
        $santriPoinTertinggi = Santri::aktif()->get()
            ->sortByDesc(fn (Santri $s) => $s->total_poin)
            ->take(10)
            ->values();

        return view('sekretaris.pelanggaran.index', compact('pelanggarans', 'santriPoinTertinggi'));
    }

    public function create()
    {
        return view('sekretaris.pelanggaran.create', [
            'santris' => Santri::aktif()->orderBy('nama_lengkap')->get(['id', 'nis', 'nama_lengkap']),
            'bobotPoin' => Pelanggaran::BOBOT_POIN,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'santri_id' => 'required|exists:santris,id',
            'jenis_pelanggaran' => 'required|string|max:255',
            'kategori' => 'required|in:Ringan,Sedang,Berat',
            'tanggal' => 'required|date',
            'sanksi' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        // Poin diisi otomatis oleh model event (lihat Pelanggaran::booted) berdasarkan kategori
        $pelanggaran = Pelanggaran::create([
            ...$validated,
            'dicatat_oleh' => auth()->id(),
        ]);

        ActivityLog::catat(
            'create',
            $pelanggaran,
            "Mencatat pelanggaran {$pelanggaran->kategori} ({$pelanggaran->poin} poin) untuk santri {$pelanggaran->santri->nama_lengkap}"
        );

        return redirect()->route('sekretaris.pelanggaran.index')->with('success', 'Pelanggaran berhasil dicatat.');
    }

    public function show(Pelanggaran $pelanggaran)
    {
        $pelanggaran->load('santri', 'pencatat');

        return view('sekretaris.pelanggaran.show', compact('pelanggaran'));
    }

    public function edit(Pelanggaran $pelanggaran)
    {
        return view('sekretaris.pelanggaran.edit', [
            'pelanggaran' => $pelanggaran,
            'bobotPoin' => Pelanggaran::BOBOT_POIN,
        ]);
    }

    public function update(Request $request, Pelanggaran $pelanggaran)
    {
        $validated = $request->validate([
            'jenis_pelanggaran' => 'required|string|max:255',
            'kategori' => 'required|in:Ringan,Sedang,Berat',
            'tanggal' => 'required|date',
            'sanksi' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        $dataSebelum = $pelanggaran->only(array_keys($validated));
        $pelanggaran->update($validated); // poin ikut ter-update otomatis lewat model event

        ActivityLog::catat('update', $pelanggaran, 'Mengubah catatan pelanggaran', $dataSebelum, $pelanggaran->getChanges());

        return redirect()->route('sekretaris.pelanggaran.index')->with('success', 'Catatan pelanggaran diperbarui.');
    }

    public function destroy(Pelanggaran $pelanggaran)
    {
        $dataSebelum = $pelanggaran->toArray();
        $pelanggaran->delete();

        ActivityLog::catat('delete', $pelanggaran, 'Menghapus catatan pelanggaran', $dataSebelum);

        return back()->with('success', 'Catatan pelanggaran dihapus.');
    }

    /**
     * Reset poin santri — tidak menghapus riwayat pelanggaran, hanya menandai titik reset baru.
     * Total poin (Santri::getTotalPoinAttribute) otomatis dihitung ulang dari titik ini.
     */
    public function resetPoin(Request $request, Santri $santri)
    {
        $validated = $request->validate([
            'alasan' => 'nullable|string|max:500',
        ]);

        $reset = ResetPoinSantri::create([
            'santri_id' => $santri->id,
            'direset_oleh' => auth()->id(),
            'alasan' => $validated['alasan'] ?? null,
            'direset_pada' => now(),
        ]);

        ActivityLog::catat('reset_poin', $santri, "Mereset poin kedisiplinan santri {$santri->nama_lengkap}", null, ['reset_id' => $reset->id]);

        return back()->with('success', "Poin kedisiplinan {$santri->nama_lengkap} berhasil direset.");
    }
}
