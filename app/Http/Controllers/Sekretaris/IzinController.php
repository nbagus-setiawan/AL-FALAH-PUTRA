<?php

namespace App\Http\Controllers\Sekretaris;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Izin;
use App\Models\JenisSurat;
use App\Models\Santri;
use App\Models\Surat;
use Illuminate\Http\Request;

class IzinController extends Controller
{
    public function index(Request $request)
    {
        $izins = Izin::with('santri')
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->santri_id, fn ($q) => $q->where('santri_id', $request->santri_id))
            ->latest('tanggal_keluar')
            ->paginate(25);

        return view('sekretaris.izin.index', compact('izins'));
    }

    public function create()
    {
        return view('sekretaris.izin.create', [
            'santris' => Santri::aktif()->orderBy('nama_lengkap')->get(['id', 'nis', 'nama_lengkap']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'santri_id' => 'required|exists:santris,id',
            'jenis_izin' => 'required|in:Sakit,Kepentingan',
            'tanggal_keluar' => 'required|date',
            'rencana_kembali' => 'required|date|after_or_equal:tanggal_keluar',
            'alasan' => 'required|string',
            'penjemput' => 'required|string|max:255',
            'kontak_penjemput' => 'required|string|max:30',
        ]);

        $izin = Izin::create([
            ...$validated,
            'status' => Izin::STATUS_PENDING,
            'diajukan_oleh' => auth()->id(),
        ]);

        ActivityLog::catat('create', $izin, "Mengajukan izin {$izin->jenis_izin} untuk santri {$izin->santri->nama_lengkap}");

        return redirect()->route('sekretaris.izin.index')->with('success', 'Pengajuan izin berhasil dikirim, menunggu persetujuan Ketua Umum.');
    }

    public function show(Izin $izin)
    {
        $izin->load('santri', 'penyetuju', 'pengaju', 'surat');

        return view('sekretaris.izin.show', compact('izin'));
    }

    /**
     * Tandai santri sudah kembali dari izin — dipanggil dari halaman detail izin yang berstatus Sedang Izin/Terlambat.
     */
    public function tandaiKembali(Izin $izin)
    {
        abort_unless(in_array($izin->status, [Izin::STATUS_SEDANG_IZIN, Izin::STATUS_TERLAMBAT]), 422, 'Status izin ini tidak valid untuk ditandai kembali.');

        $izin->update([
            'status' => Izin::STATUS_SUDAH_KEMBALI,
            'tanggal_kembali_aktual' => now()->toDateString(),
        ]);

        ActivityLog::catat('update', $izin, "Menandai santri {$izin->santri->nama_lengkap} sudah kembali dari izin");

        return back()->with('success', 'Santri ditandai sudah kembali.');
    }

    public function destroy(Izin $izin)
    {
        abort_if($izin->status !== Izin::STATUS_PENDING, 422, 'Hanya pengajuan berstatus Pending yang bisa dibatalkan.');

        $dataSebelum = $izin->toArray();
        $izin->delete();

        ActivityLog::catat('delete', $izin, "Membatalkan pengajuan izin santri {$izin->santri->nama_lengkap}", $dataSebelum);

        return back()->with('success', 'Pengajuan izin dibatalkan.');
    }

    /**
     * Generate Surat Izin langsung dari data izin yang sudah Approved (PRD 5.5).
     * Nomor surat otomatis via Surat::generateNomor(), lalu ditautkan balik ke izin lewat kolom surat_id.
     */
    public function generateSurat(Izin $izin)
    {
        abort_if($izin->status === Izin::STATUS_PENDING || $izin->status === Izin::STATUS_REJECTED, 422, 'Izin harus disetujui terlebih dahulu sebelum surat bisa dibuat.');
        abort_if($izin->surat_id !== null, 422, 'Surat izin untuk pengajuan ini sudah pernah dibuat.');

        $jenisSuratIzin = JenisSurat::where('kode', 'SI')->firstOrFail(); // kode "SI" = Surat Izin, lihat seeder JenisSuratSeeder

        $tanggal = now();
        $nomor = Surat::generateNomor($jenisSuratIzin, $tanggal);

        $isiRingkas = "Surat izin {$izin->jenis_izin} atas nama {$izin->santri->nama_lengkap} (NIS {$izin->santri->nis}), "
            ."keluar pondok {$izin->tanggal_keluar->format('d-m-Y')}, rencana kembali {$izin->rencana_kembali->format('d-m-Y')}.";

        $surat = Surat::create([
            ...$nomor,
            'jenis_surat_id' => $jenisSuratIzin->id,
            'arah' => 'Keluar',
            'tanggal' => $tanggal->toDateString(),
            'perihal' => 'Surat Izin Santri',
            'tujuan_pengirim' => $izin->penjemput,
            'isi_ringkas' => $isiRingkas,
            'santri_id' => $izin->santri_id,
            'status' => 'Diarsipkan',
            'status_approval' => Surat::APPROVAL_APPROVED, // surat izin turunan dari izin yang sudah disetujui, tidak perlu approval terpisah
            'disetujui_oleh' => $izin->disetujui_oleh,
            'disetujui_pada' => $izin->disetujui_pada,
            'dibuat_oleh' => auth()->id(),
        ]);

        $izin->update(['surat_id' => $surat->id]);

        ActivityLog::catat('generate_surat', $izin, "Membuat Surat Izin {$surat->nomor_surat} untuk santri {$izin->santri->nama_lengkap}");

        return redirect()
            ->route('sekretaris.izin.show', $izin)
            ->with('success', "Surat Izin berhasil dibuat dengan nomor {$surat->nomor_surat}.");
    }
}
