<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asrama extends Model
{
    use HasFactory;

    protected $table = 'asramas';

    protected $fillable = ['nama', 'kapasitas', 'penanggung_jawab_id'];

    public function penanggungJawab()
    {
        return $this->belongsTo(Pengurus::class, 'penanggung_jawab_id');
    }

    public function riwayatPenghuni()
    {
        return $this->hasMany(AsramaPenghuni::class);
    }

    // Penghuni yang sedang aktif saat ini (tanggal_keluar masih null)
    public function penghuniAktif()
    {
        return $this->hasMany(AsramaPenghuni::class)->whereNull('tanggal_keluar');
    }

    public function jumlahPenghuniAktif(): int
    {
        return $this->penghuniAktif()->count();
    }
}
