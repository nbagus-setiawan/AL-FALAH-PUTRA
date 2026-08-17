<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengurus extends Model
{
    use HasFactory;

    protected $table = 'pengurus';

    protected $fillable = [
        'nama', 'nip_internal', 'jabatan', 'periode', 'kontak', 'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function asramaDiawasi()
    {
        return $this->hasMany(Asrama::class, 'penanggung_jawab_id');
    }
}
