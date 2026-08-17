<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubKategoriRapb extends Model
{
    use HasFactory;

    protected $table = 'sub_kategori_rapbs';

    protected $fillable = ['kategori_rapb_id', 'nama'];

    public function kategori()
    {
        return $this->belongsTo(KategoriRapb::class, 'kategori_rapb_id');
    }

    public function itemRincians()
    {
        return $this->hasMany(ItemRincian::class);
    }
}
