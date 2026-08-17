<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Izin extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'Pending';
    public const STATUS_REJECTED = 'Rejected';
    public const STATUS_APPROVED = 'Approved';
    public const STATUS_SEDANG_IZIN = 'Sedang Izin';
    public const STATUS_SUDAH_KEMBALI = 'Sudah Kembali';
    public const STATUS_TERLAMBAT = 'Terlambat';

    protected $fillable = [
        'santri_id', 'jenis_izin', 'tanggal_keluar', 'rencana_kembali', 'tanggal_kembali_aktual',
        'alasan', 'penjemput', 'kontak_penjemput', 'status',
        'disetujui_oleh', 'disetujui_pada', 'catatan_penolakan',
        'surat_id', 'diajukan_oleh', 'notifikasi_telat_terkirim',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_keluar' => 'date',
            'rencana_kembali' => 'date',
            'tanggal_kembali_aktual' => 'date',
            'disetujui_pada' => 'datetime',
            'notifikasi_telat_terkirim' => 'boolean',
        ];
    }

    public function santri()
    {
        return $this->belongsTo(Santri::class);
    }

    public function surat()
    {
        return $this->belongsTo(Surat::class);
    }

    public function penyetuju()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    public function pengaju()
    {
        return $this->belongsTo(User::class, 'diajukan_oleh');
    }

    // Izin yang sudah lewat rencana_kembali tapi belum kembali -> kandidat notifikasi telat
    public function scopeTerlambatBelumNotif($query)
    {
        return $query->where('status', self::STATUS_SEDANG_IZIN)
            ->whereDate('rencana_kembali', '<', now()->toDateString())
            ->where('notifikasi_telat_terkirim', false);
    }
}
