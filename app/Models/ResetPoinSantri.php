<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResetPoinSantri extends Model
{
    use HasFactory;

    protected $table = 'reset_poin_santris';

    protected $fillable = ['santri_id', 'direset_oleh', 'alasan', 'direset_pada'];

    protected function casts(): array
    {
        return ['direset_pada' => 'datetime'];
    }

    public function santri()
    {
        return $this->belongsTo(Santri::class);
    }

    public function pereset()
    {
        return $this->belongsTo(User::class, 'direset_oleh');
    }
}
