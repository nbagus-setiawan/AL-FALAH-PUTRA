<?php

namespace App\Http\Controllers\Sekretaris;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Kelas;
use App\Models\Santri;
use App\Models\Tingkat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KenaikanKelasController extends Controller
{
    /**
     * Langkah 1: tampilkan preview seluruh santri aktif — kelas saat ini -> kelas tujuan (otomatis naik 1 tingkat).
     * Sistem mencoba mencocokkan kelas tujuan berdasarkan nama kelas yang sama di tingkat berikutnya
     * dan tahun ajaran baru; jika tidak ditemukan, Sekretaris bisa memilih manual di form.
     */
    public function preview(Request $request)
    {
        $tahunAjaranBaru = $request->get('tahun_ajaran_baru');

        $santris = Santri::aktif()->with('kelas.tingkat')->orderBy('nama_lengkap')->get();

        $preview = $santris->map(function (Santri $santri) use ($tahunAjaranBaru) {
            $kelasSekarang = $santri->kelas;
            $tingkatBerikutnya = $kelasSekarang?->tingkat?->tingkatBerikutnya();

            $kelasTujuan = null;
            if ($tingkatBerikutnya && $tahunAjaranBaru) {
                $kelasTujuan = Kelas::where('tingkat_id', $tingkatBerikutnya->id)
                    ->where('nama', $kelasSekarang->nama)
                    ->where('tahun_ajaran', $tahunAjaranBaru)
                    ->first();
            }

            return [
                'santri' => $santri,
                'kelas_sekarang' => $kelasSekarang,
                'tingkat_berikutnya' => $tingkatBerikutnya,
                'kelas_tujuan' => $kelasTujuan,
                'is_kelas_tertinggi' => $tingkatBerikutnya === null, // kandidat "Lulus"
            ];
        });

        return view('sekretaris.kenaikan-kelas.preview', [
            'preview' => $preview,
            'tahunAjaranBaru' => $tahunAjaranBaru,
            'kelasList' => Kelas::with('tingkat')->orderBy('tahun_ajaran', 'desc')->orderBy('nama')->get(),
        ]);
    }

    /**
     * Langkah 2-3: proses kenaikan kelas massal.
     * Payload dari form preview, contoh struktur:
     *   promosi: [ santri_id => kelas_tujuan_id, ... ]   (santri yang naik/pindah kelas)
     *   tinggal_kelas: [ santri_id, ... ]                (di-uncheck, tetap di kelas & tingkat sekarang)
     *   lulus: [ santri_id, ... ]                        (santri kelas tertinggi ditandai Lulus)
     */
    public function proses(Request $request)
    {
        $validated = $request->validate([
            'promosi' => 'array',
            'promosi.*' => 'exists:kelas,id',
            'tinggal_kelas' => 'array',
            'tinggal_kelas.*' => 'exists:santris,id',
            'lulus' => 'array',
            'lulus.*' => 'exists:santris,id',
        ]);

        // FIX: validasi 'promosi.*' di atas hanya mengecek VALUE (kelas_tujuan_id).
        // Key array (santri_id) tidak tervalidasi otomatis oleh Laravel, jadi divalidasi manual di sini
        // supaya tidak ada santri_id "siluman" yang lolos ke proses update.
        if (! empty($validated['promosi'])) {
            $santriIdsValid = Santri::whereIn('id', array_keys($validated['promosi']))->pluck('id')->all();
            $santriIdsTidakValid = array_diff(array_keys($validated['promosi']), $santriIdsValid);

            abort_if(
                ! empty($santriIdsTidakValid),
                422,
                'Terdapat data santri yang tidak valid pada daftar promosi.'
            );
        }

        $jumlahDiproses = DB::transaction(function () use ($validated) {
            $count = 0;

            foreach ($validated['promosi'] ?? [] as $santriId => $kelasTujuanId) {
                // FIX: hanya hitung santri yang baris-nya benar-benar ter-update
                // (update() mengembalikan jumlah baris terdampak), bukan diinkrement tanpa syarat.
                $count += Santri::where('id', $santriId)->update(['kelas_id' => $kelasTujuanId]);
            }

            foreach ($validated['lulus'] ?? [] as $santriId) {
                $count += Santri::where('id', $santriId)->update([
                    'status' => Santri::STATUS_LULUS,
                    'tanggal_keluar' => now()->toDateString(),
                    'keterangan_keluar' => 'Lulus — tamat seluruh jenjang pendidikan',
                ]);
            }

            // tinggal_kelas: tidak ada perubahan data, hanya dikecualikan dari update kelas_id

            return $count;
        });

        ActivityLog::catat(
            'bulk_promosi',
            new Santri, // subjek generik karena melibatkan banyak santri sekaligus
            "Memproses kenaikan kelas / tahun ajaran baru untuk {$jumlahDiproses} santri",
            null,
            ['jumlah_diproses' => $jumlahDiproses]
        );

        return redirect()
            ->route('sekretaris.kenaikan-kelas.preview')
            ->with('success', "Kenaikan kelas berhasil diproses untuk {$jumlahDiproses} santri.");
    }
}