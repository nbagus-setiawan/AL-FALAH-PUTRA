<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriRapb extends Model
{
    use HasFactory;

    protected $table = 'kategori_rapbs';

    protected $fillable = ['tahun_anggaran_id', 'nama', 'jenis'];

    public function tahunAnggaran()
    {
        return $this->belongsTo(TahunAnggaran::class);
    }

    public function subKategoris()
    {
        return $this->hasMany(SubKategoriRapb::class);
    }
}
