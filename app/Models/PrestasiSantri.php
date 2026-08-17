<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrestasiSantri extends Model
{
    use HasFactory;

    protected $table = 'prestasi_santris';

    protected $fillable = ['santri_id', 'nama_prestasi', 'tingkat', 'tanggal', 'keterangan'];

    protected function casts(): array
    {
        return ['tanggal' => 'date'];
    }

    public function santri()
    {
        return $this->belongsTo(Santri::class);
    }
}
