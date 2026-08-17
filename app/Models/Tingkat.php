<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tingkat extends Model
{
    use HasFactory;

    protected $fillable = ['nama', 'urutan'];

    public function kelas()
    {
        return $this->hasMany(Kelas::class);
    }

    // Tingkat berikutnya, dipakai saat proses Kenaikan Kelas (bulk promosi)
    public function tingkatBerikutnya()
    {
        return static::where('urutan', '>', $this->urutan)->orderBy('urutan')->first();
    }
}
