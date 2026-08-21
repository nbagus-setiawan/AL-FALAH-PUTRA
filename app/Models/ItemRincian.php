<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemRincian extends Model
{
    use HasFactory;

    protected $table = 'item_rincians';

    protected $fillable = ['sub_kategori_rapb_id', 'nama', 'jumlah_rencana', 'keterangan'];

    protected function casts(): array
    {
        return ['jumlah_rencana' => 'decimal:2'];
    }

    public function subKategori()
    {
        return $this->belongsTo(SubKategoriRapb::class, 'sub_kategori_rapb_id');
    }

    public function rencanaBulanans()
    {
        return $this->hasMany(RencanaBulanan::class);
    }

    public function realisasis()
    {
        return $this->hasMany(Realisasi::class);
    }

    /**
     * PERFORMA: pakai koleksi 'realisasis' yang sudah di-eager-load (properti, bukan
     * method) bila tersedia, supaya tidak fire query baru per item saat dipanggil
     * dalam loop (mis. daftar item di halaman detail kategori/tahun anggaran yang
     * sudah ->with('...itemRincians.realisasis')). Fallback ke query langsung kalau
     * relasi belum dimuat (mis. akses satu ItemRincian saja).
     */
    public function getTotalRealisasiAttribute(): float
    {
        if ($this->relationLoaded('realisasis')) {
            return (float) $this->realisasis->sum('jumlah');
        }

        return (float) $this->realisasis()->sum('jumlah');
    }

    public function getSelisihAttribute(): float
    {
        return (float) $this->jumlah_rencana - $this->total_realisasi;
    }

    public function getPersentaseSerapanAttribute(): float
    {
        if ((float) $this->jumlah_rencana <= 0) {
            return 0;
        }

        return round(($this->total_realisasi / (float) $this->jumlah_rencana) * 100, 2);
    }
}