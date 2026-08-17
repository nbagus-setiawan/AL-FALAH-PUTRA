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

    public function getTotalRealisasiAttribute(): float
    {
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
