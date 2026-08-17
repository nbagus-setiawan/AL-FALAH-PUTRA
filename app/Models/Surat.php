<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Surat extends Model
{
    use HasFactory;

    public const APPROVAL_PENDING = 'Pending';
    public const APPROVAL_APPROVED = 'Approved';
    public const APPROVAL_REJECTED = 'Rejected';

    protected $fillable = [
        'nomor_surat', 'nomor_urut', 'tahun', 'jenis_surat_id', 'arah', 'tanggal',
        'perihal', 'tujuan_pengirim', 'isi_ringkas', 'isi_lengkap',
        'lampiran_path', 'file_pdf_path', 'santri_id', 'status', 'dibuat_oleh',
        'status_approval', 'disetujui_oleh', 'disetujui_pada', 'catatan_penolakan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'disetujui_pada' => 'datetime',
        ];
    }

    public function penyetuju()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    public function jenisSurat()
    {
        return $this->belongsTo(JenisSurat::class);
    }

    public function santri()
    {
        return $this->belongsTo(Santri::class);
    }

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    /**
     * Konversi angka bulan (1-12) ke angka Romawi, dipakai di format nomor surat.
     */
    public static function bulanRomawi(int $bulan): string
    {
        $romawi = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];

        return $romawi[$bulan - 1] ?? '';
    }

    /**
     * Generate nomor surat berikutnya untuk tahun berjalan.
     * Nomor urut KONTINU untuk semua jenis surat dalam satu tahun, reset ke 1 setiap 1 Januari.
     *
     * Menggunakan row lock (lockForUpdate) di dalam transaction agar aman dari race condition
     * ketika dua user membuat surat secara bersamaan.
     */
    public static function generateNomor(JenisSurat $jenisSurat, \DateTimeInterface $tanggal): array
    {
        return DB::transaction(function () use ($jenisSurat, $tanggal) {
            $tahun = (int) $tanggal->format('Y');

            $nomorTerakhir = static::where('tahun', $tahun)
                ->lockForUpdate()
                ->max('nomor_urut');

            $nomorUrut = ($nomorTerakhir ?? 0) + 1;

            $nomorFormatted = str_pad((string) $nomorUrut, 3, '0', STR_PAD_LEFT);
            $bulanRomawi = static::bulanRomawi((int) $tanggal->format('n'));

            $nomorSurat = "{$nomorFormatted}/AFP/{$jenisSurat->kode}/{$bulanRomawi}/{$tahun}";

            return [
                'nomor_surat' => $nomorSurat,
                'nomor_urut' => $nomorUrut,
                'tahun' => $tahun,
            ];
        });
    }
}
