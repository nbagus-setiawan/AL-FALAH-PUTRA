<?php

namespace App\Http\Controllers\Sekretaris;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Kelas;
use App\Models\Santri;
use Illuminate\Http\Request;

class SantriController extends Controller
{
    public function index(Request $request)
    {
        $santris = Santri::with('kelas.tingkat')
            ->when($request->q, fn ($q) => $q->where('nama_lengkap', 'like', "%{$request->q}%")
                ->orWhere('nis', 'like', "%{$request->q}%"))
            ->when($request->kelas_id, fn ($q) => $q->where('kelas_id', $request->kelas_id))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->orderBy('nama_lengkap')
            ->paginate(25);

        // Defense-in-depth: field sensitif (NIK, riwayat kesehatan, alergi, gol. darah)
        // disembunyikan dari serialisasi kalau user yang login bukan Sekretaris.
        // Saat ini route ini sudah di-guard middleware('role:sekretaris') sehingga
        // secara praktis selalu Sekretaris, TAPI kalau nanti Ketua Umum diberi akses
        // baca ke modul Santri, filter ini mencegah data sensitif ikut bocor tanpa
        // perlu mengubah controller lagi.
        if (! $request->user()->canViewDataSensitifSantri()) {
            $santris->getCollection()->each->makeHidden(Santri::FIELD_SENSITIF);
        }

        return view('sekretaris.santri.index', [
            'santris' => $santris,
            'kelasList' => Kelas::orderBy('nama')->get(),
        ]);
    }

    public function create()
    {
        return view('sekretaris.santri.create', [
            'kelasList' => Kelas::with('tingkat')->orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        if ($request->hasFile('foto')) {
            // Foto disimpan di disk publik terbatas (bukan data sensitif), maks 2MB sesuai kebutuhan non-fungsional
            $validated['foto_path'] = $request->file('foto')->store('santri/foto', 'public');
        }

        $santri = Santri::create($validated);

        ActivityLog::catat('create', $santri, "Menambahkan data santri {$santri->nama_lengkap} (NIS {$santri->nis})");

        return redirect()->route('sekretaris.santri.index')->with('success', 'Data santri berhasil ditambahkan.');
    }

    public function show(Request $request, Santri $santri)
    {
        $santri->load(['kelas.tingkat', 'riwayatAsrama.asrama', 'prestasis', 'izins', 'pelanggarans']);

        if (! $request->user()->canViewDataSensitifSantri()) {
            $santri->makeHidden(Santri::FIELD_SENSITIF);
        }

        return view('sekretaris.santri.show', compact('santri'));
    }

    public function edit(Santri $santri)
    {
        return view('sekretaris.santri.edit', [
            'santri' => $santri,
            'kelasList' => Kelas::with('tingkat')->orderBy('nama')->get(),
        ]);
    }

    public function update(Request $request, Santri $santri)
    {
        $validated = $this->validated($request, $santri->id);
        $dataSebelum = $santri->only(array_keys($validated));

        if ($request->hasFile('foto')) {
            $validated['foto_path'] = $request->file('foto')->store('santri/foto', 'public');
        }

        $santri->update($validated);

        ActivityLog::catat('update', $santri, "Mengubah data santri {$santri->nama_lengkap}", $dataSebelum, $santri->getChanges());

        return redirect()->route('sekretaris.santri.index')->with('success', 'Data santri berhasil diperbarui.');
    }

    public function destroy(Santri $santri)
    {
        $dataSebelum = $santri->toArray();
        $nama = $santri->nama_lengkap;
        $santri->delete();

        ActivityLog::catat('delete', $santri, "Menghapus data santri {$nama}", $dataSebelum);

        return back()->with('success', 'Data santri berhasil dihapus.');
    }

    /**
     * Validasi bersama create/update.
     * Field sensitif (nik, riwayat_kesehatan, alergi, golongan_darah) hanya diisi lewat form ini
     * karena route ini sudah di-guard middleware role:sekretaris — satu-satunya role yang boleh akses.
     */
    protected function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'nis' => 'required|string|max:30|unique:santris,nis'.($ignoreId ? ",{$ignoreId}" : ''),
            'nisn' => 'nullable|string|max:30',
            'nama_lengkap' => 'required|string|max:255',
            'nama_panggilan' => 'nullable|string|max:100',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

            'alamat' => 'nullable|string',
            'provinsi' => 'nullable|string|max:100',
            'kabupaten_kota' => 'nullable|string|max:100',

            'nama_ayah' => 'nullable|string|max:255',
            'pekerjaan_ayah' => 'nullable|string|max:100',
            'nama_ibu' => 'nullable|string|max:255',
            'pekerjaan_ibu' => 'nullable|string|max:100',
            'nama_wali' => 'nullable|string|max:255',
            'kontak_wali' => 'nullable|string|max:30',

            'kontak_darurat_nama' => 'nullable|string|max:255',
            'kontak_darurat_hubungan' => 'nullable|string|max:100',
            'kontak_darurat_telepon' => 'nullable|string|max:30',

            'nik' => 'nullable|string|max:20',
            'riwayat_kesehatan' => 'nullable|string',
            'alergi' => 'nullable|string',
            'golongan_darah' => 'nullable|string|max:3',

            'kelas_id' => 'nullable|exists:kelas,id',
            'status' => 'required|in:Aktif,Lulus,Boyong',
            'tanggal_masuk' => 'nullable|date',
            'tanggal_keluar' => 'nullable|date',
            'keterangan_keluar' => 'nullable|string',
        ]);
    }
}