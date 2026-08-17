<?php

namespace App\Http\Controllers\Sekretaris;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\JenisSurat;
use App\Models\Santri;
use App\Models\Surat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SuratController extends Controller
{
    public function index(Request $request)
    {
        $surats = Surat::with(['jenisSurat', 'santri'])
            ->when($request->q, fn ($q) => $q->where('perihal', 'like', "%{$request->q}%")
                ->orWhere('nomor_surat', 'like', "%{$request->q}%"))
            ->when($request->jenis_surat_id, fn ($q) => $q->where('jenis_surat_id', $request->jenis_surat_id))
            ->latest('tanggal')
            ->paginate(20);

        return view('sekretaris.surat.index', [
            'surats' => $surats,
            'jenisSurats' => JenisSurat::orderBy('nama')->get(),
        ]);
    }

    public function create()
    {
        return view('sekretaris.surat.create', [
            'jenisSurats' => JenisSurat::orderBy('nama')->get(),
            'santris' => Santri::aktif()->orderBy('nama_lengkap')->get(['id', 'nis', 'nama_lengkap']),
        ]);
    }

    /**
     * Simpan surat keluar dari Generator Surat.
     * Nomor otomatis dibuat di sini via Surat::generateNomor() — lihat app/Models/Surat.php.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis_surat_id' => 'required|exists:jenis_surats,id',
            'tanggal' => 'required|date',
            'perihal' => 'required|string|max:255',
            'tujuan_pengirim' => 'required|string|max:255',
            'isi_lengkap' => 'required|string', // hasil rich text editor (TinyMCE/Quill)
            'santri_id' => 'nullable|exists:santris,id',
            'lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $jenisSurat = JenisSurat::findOrFail($validated['jenis_surat_id']);
        $tanggal = new \DateTime($validated['tanggal']);

        $nomor = Surat::generateNomor($jenisSurat, $tanggal);

        $lampiranPath = null;
        if ($request->hasFile('lampiran')) {
            // Disimpan di disk privat (bukan public) sesuai kebutuhan non-fungsional keamanan file
            $lampiranPath = $request->file('lampiran')->store('surat/lampiran', 'private');
        }

        $surat = Surat::create([
            ...$nomor,
            'jenis_surat_id' => $jenisSurat->id,
            'arah' => 'Keluar',
            'tanggal' => $validated['tanggal'],
            'perihal' => $validated['perihal'],
            'tujuan_pengirim' => $validated['tujuan_pengirim'],
            'isi_lengkap' => $validated['isi_lengkap'],
            'santri_id' => $validated['santri_id'] ?? null,
            'lampiran_path' => $lampiranPath,
            'status' => 'Diarsipkan', // langsung masuk arsip untuk pencarian; status_approval terpisah mengatur persetujuan
            'status_approval' => Surat::APPROVAL_PENDING,
            'dibuat_oleh' => auth()->id(),
        ]);

        ActivityLog::catat(
            'create',
            $surat,
            "Membuat surat keluar {$surat->nomor_surat} — {$surat->perihal}, menunggu persetujuan Ketua Umum",
        );

        return redirect()
            ->route('sekretaris.surat.index')
            ->with('success', "Surat berhasil dibuat dengan nomor {$surat->nomor_surat}, menunggu persetujuan Ketua Umum.");
    }

    public function show(Surat $surat)
    {
        $surat->load(['jenisSurat', 'santri', 'pembuat']);

        return view('sekretaris.surat.show', compact('surat'));
    }

    /**
     * Export PDF surat via DomPDF — kompatibel shared hosting (bukan wkhtmltopdf).
     * Perlu: composer require barryvdh/laravel-dompdf
     */
    public function cetakPdf(Surat $surat)
    {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('sekretaris.surat.pdf', compact('surat'));

        return $pdf->stream("{$surat->nomor_surat}.pdf");
    }

    /**
     * Stream lampiran surat dari disk privat.
     * Route ini didaftarkan di dua tempat (Sekretaris & Ketua Umum) karena
     * kedua role tersebut sama-sama perlu membuka lampiran — Sekretaris saat
     * mengelola arsip, Ketua Umum saat meninjau surat yang menunggu approval.
     * File TIDAK boleh di-symlink ke public/storage (lihat config/filesystems.php).
     */
    public function lampiran(Surat $surat)
    {
        abort_if(empty($surat->lampiran_path), 404, 'Surat ini tidak memiliki lampiran.');
        abort_unless(Storage::disk('private')->exists($surat->lampiran_path), 404, 'File lampiran tidak ditemukan.');

        return Storage::disk('private')->response(
            $surat->lampiran_path,
            "lampiran-{$surat->nomor_surat}.".pathinfo($surat->lampiran_path, PATHINFO_EXTENSION)
        );
    }
}