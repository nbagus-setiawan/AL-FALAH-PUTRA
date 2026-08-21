<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\TahunAnggaran;
use Illuminate\Http\Request;

class TahunAnggaranController extends Controller
{
    public function index()
    {
        $tahunAnggarans = TahunAnggaran::orderByDesc('tanggal_mulai')->paginate(10);

        return view('bendahara.tahun-anggaran.index', compact('tahunAnggarans'));
    }

    public function create()
    {
        return view('bendahara.tahun-anggaran.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        // Hanya satu tahun anggaran yang aktif pada satu waktu
        if (! empty($validated['is_active'])) {
            TahunAnggaran::where('is_active', true)->update(['is_active' => false]);
        }

        $tahunAnggaran = TahunAnggaran::create($validated);

        ActivityLog::catat('create', $tahunAnggaran, "Membuat tahun anggaran {$tahunAnggaran->nama}");

        return redirect()->route('bendahara.tahun-anggaran.show', $tahunAnggaran)->with('success', 'Tahun anggaran berhasil dibuat.');
    }

    public function show(TahunAnggaran $tahunAnggaran)
    {
        $tahunAnggaran->load('kategoris.subKategoris.itemRincians.realisasis');

        return view('bendahara.tahun-anggaran.show', [
            'tahunAnggaran' => $tahunAnggaran,
            'totalRencana' => $tahunAnggaran->totalRencana(),
            'totalRealisasi' => $tahunAnggaran->totalRealisasi(),
        ]);
    }

    public function edit(TahunAnggaran $tahunAnggaran)
    {
        return view('bendahara.tahun-anggaran.edit', compact('tahunAnggaran'));
    }

    public function update(Request $request, TahunAnggaran $tahunAnggaran)
    {
        // FIX: kunci perubahan setelah disetujui Ketua Umum — cegah approval jadi tidak
        // bermakna karena data berubah diam-diam. Bendahara harus memakai alur
        // "ajukan ulang" (lihat ajukanApproval()) sebelum bisa mengedit lagi.
        abort_if(
            $tahunAnggaran->isApprovalLocked(),
            422,
            'RAPB ini sudah disetujui Ketua Umum dan terkunci dari perubahan. Ajukan ulang terlebih dahulu jika ingin merevisi.'
        );

        $validated = $this->validated($request);
        $dataSebelum = $tahunAnggaran->only(array_keys($validated));

        if (! empty($validated['is_active'])) {
            TahunAnggaran::where('is_active', true)->where('id', '!=', $tahunAnggaran->id)->update(['is_active' => false]);
        }

        $tahunAnggaran->update($validated);

        ActivityLog::catat('update', $tahunAnggaran, "Mengubah tahun anggaran {$tahunAnggaran->nama}", $dataSebelum, $tahunAnggaran->getChanges());

        return redirect()->route('bendahara.tahun-anggaran.show', $tahunAnggaran)->with('success', 'Tahun anggaran diperbarui.');
    }

    public function destroy(TahunAnggaran $tahunAnggaran)
    {
        abort_if($tahunAnggaran->kategoris()->exists(), 422, 'Tahun anggaran ini masih memiliki data kategori/rincian.');

        $dataSebelum = $tahunAnggaran->toArray();
        $nama = $tahunAnggaran->nama;
        $tahunAnggaran->delete();

        ActivityLog::catat('delete', $tahunAnggaran, "Menghapus tahun anggaran {$nama}", $dataSebelum);

        return back()->with('success', 'Tahun anggaran dihapus.');
    }

    /**
     * Bendahara mengajukan RAPB untuk disetujui Ketua Umum (status Pending sudah default sejak dibuat,
     * route ini dipakai jika sebelumnya sempat direset/ditolak/dikunci dan Bendahara ingin mengajukan ulang
     * — termasuk setelah revisi pada RAPB yang tadinya sudah Approved).
     */
    public function ajukanApproval(TahunAnggaran $tahunAnggaran)
    {
        $tahunAnggaran->update([
            'status_approval' => 'Pending',
            'disetujui_oleh' => null,
            'disetujui_pada' => null,
            'catatan_penolakan' => null,
        ]);

        ActivityLog::catat('ajukan_approval', $tahunAnggaran, "Mengajukan ulang RAPB {$tahunAnggaran->nama} untuk persetujuan");

        return back()->with('success', 'RAPB diajukan untuk persetujuan Ketua Umum.');
    }

    /**
     * Cetak LPJ PDF — per tahun anggaran, via DomPDF (kompatibel shared hosting).
     */
    public function cetakLpj(TahunAnggaran $tahunAnggaran)
    {
        $tahunAnggaran->load('kategoris.subKategoris.itemRincians.realisasis');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('bendahara.tahun-anggaran.lpj-pdf', [
            'tahunAnggaran' => $tahunAnggaran,
            'totalRencana' => $tahunAnggaran->totalRencana(),
            'totalRealisasi' => $tahunAnggaran->totalRealisasi(),
        ]);

        return $pdf->stream("LPJ-RAPB-{$tahunAnggaran->nama}.pdf");
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'nama' => 'required|string|max:20',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'is_active' => 'boolean',
        ]);
    }
}