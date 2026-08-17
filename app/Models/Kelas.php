<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';

    protected $fillable = ['tingkat_id', 'nama', 'tahun_ajaran'];

    public function tingkat()
    {
        return $this->belongsTo(Tingkat::class);
    }

    public function santris()
    {
        return $this->hasMany(Santri::class);
    }
}
