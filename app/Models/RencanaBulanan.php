<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RencanaBulanan extends Model
{
    use HasFactory;

    protected $fillable = ['item_rincian_id', 'bulan', 'jumlah'];

    protected function casts(): array
    {
        return ['jumlah' => 'decimal:2'];
    }

    public function itemRincian()
    {
        return $this->belongsTo(ItemRincian::class);
    }
}
