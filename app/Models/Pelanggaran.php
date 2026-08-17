<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'santri_id', 'jenis_pelanggaran', 'kategori', 'poin', 'tanggal', 'sanksi', 'catatan', 'dicatat_oleh',
    ];

    protected function casts(): array
    {
        return ['tanggal' => 'date'];
    }

    // Bobot poin tetap sesuai PRD 5.7
    public const BOBOT_POIN = [
        'Ringan' => 10,
        'Sedang' => 20,
        'Berat' => 30,
    ];

    protected static function booted()
    {
        // Poin selalu mengikuti bobot tetap berdasarkan kategori, tidak bisa diisi manual berbeda
        static::saving(function (Pelanggaran $pelanggaran) {
            $pelanggaran->poin = self::BOBOT_POIN[$pelanggaran->kategori] ?? 0;
        });
    }

    public function santri()
    {
        return $this->belongsTo(Santri::class);
    }

    public function pencatat()
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }
}
