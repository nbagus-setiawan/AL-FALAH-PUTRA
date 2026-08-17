<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Santri extends Model
{
    use HasFactory;

    public const STATUS_AKTIF = 'Aktif';
    public const STATUS_LULUS = 'Lulus';
    public const STATUS_BOYONG = 'Boyong';

    protected $fillable = [
        'nis', 'nisn', 'nama_lengkap', 'nama_panggilan', 'jenis_kelamin',
        'tempat_lahir', 'tanggal_lahir', 'foto_path',
        'alamat', 'provinsi', 'kabupaten_kota',
        'nama_ayah', 'pekerjaan_ayah', 'nama_ibu', 'pekerjaan_ibu', 'nama_wali', 'kontak_wali',
        'kontak_darurat_nama', 'kontak_darurat_hubungan', 'kontak_darurat_telepon',
        'nik', 'riwayat_kesehatan', 'alergi', 'golongan_darah',
        'kelas_id', 'status', 'tanggal_masuk', 'tanggal_keluar', 'keterangan_keluar',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'tanggal_masuk' => 'date',
            'tanggal_keluar' => 'date',
        ];
    }

    // Data sensitif yang hanya boleh diserialisasikan untuk role Sekretaris.
    // Dipakai di Controller/Resource, BUKAN otomatis di $hidden, karena kebutuhan akses berbeda per konteks.
    public const FIELD_SENSITIF = ['nik', 'riwayat_kesehatan', 'alergi', 'golongan_darah'];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function prestasis()
    {
        return $this->hasMany(PrestasiSantri::class);
    }

    public function riwayatAsrama()
    {
        return $this->hasMany(AsramaPenghuni::class)->orderByDesc('tanggal_masuk');
    }

    public function asramaSaatIni()
    {
        return $this->hasOne(AsramaPenghuni::class)->whereNull('tanggal_keluar');
    }

    public function izins()
    {
        return $this->hasMany(Izin::class);
    }

    public function izinAktif()
    {
        return $this->hasOne(Izin::class)->whereIn('status', ['Sedang Izin', 'Terlambat'])->latestOfMany();
    }

    public function pelanggarans()
    {
        return $this->hasMany(Pelanggaran::class);
    }

    public function resetPoinHistory()
    {
        return $this->hasMany(ResetPoinSantri::class);
    }

    public function surats()
    {
        return $this->hasMany(Surat::class);
    }

    /**
     * Total poin kedisiplinan akumulatif, dihitung dari titik reset terakhir (jika ada).
     */
    public function getTotalPoinAttribute(): int
    {
        $query = $this->pelanggarans();

        $resetTerakhir = $this->resetPoinHistory()->orderByDesc('direset_pada')->first();
        if ($resetTerakhir) {
            $query->where('tanggal', '>=', $resetTerakhir->direset_pada);
        }

        return (int) $query->sum('poin');
    }

    /**
     * Warna indikator berdasarkan total poin: <=49 Hijau, 50-99 Kuning, >=100 Merah.
     */
    public function getWarnaPoinAttribute(): string
    {
        $total = $this->total_poin;

        return match (true) {
            $total >= 100 => 'Merah',
            $total >= 50 => 'Kuning',
            default => 'Hijau',
        };
    }

    public function scopeAktif($query)
    {
        return $query->where('status', self::STATUS_AKTIF);
    }
}
