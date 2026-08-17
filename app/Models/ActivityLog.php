<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'action', 'subject_type', 'subject_id', 'description',
        'data_sebelum', 'data_sesudah', 'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'data_sebelum' => 'array',
            'data_sesudah' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Catat satu entri audit trail. Dipanggil dari Controller/Observer setiap kali
     * ada create/update/delete/approve/reject pada data penting.
     *
     * Contoh:
     *   ActivityLog::catat('update', $santri, 'Mengubah data santri ' . $santri->nama_lengkap, $dataSebelum, $santri->getChanges());
     */
    public static function catat(
        string $action,
        Model $subject,
        string $description,
        ?array $dataSebelum = null,
        ?array $dataSesudah = null
    ): self {
        return static::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'subject_type' => get_class($subject),
            'subject_id' => $subject->getKey(),
            'description' => $description,
            'data_sebelum' => $dataSebelum,
            'data_sesudah' => $dataSesudah,
            'ip_address' => Request::ip(),
        ]);
    }
}
