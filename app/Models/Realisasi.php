<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Realisasi extends Model
{
    use HasFactory;

    protected $table = 'realisasis';

    protected $fillable = ['item_rincian_id', 'jumlah', 'tanggal', 'bukti_path', 'keterangan', 'dicatat_oleh'];

    protected function casts(): array
    {
        return [
            'jumlah' => 'decimal:2',
            'tanggal' => 'date',
        ];
    }

    public function itemRincian()
    {
        return $this->belongsTo(ItemRincian::class);
    }

    public function pencatat()
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }
}
