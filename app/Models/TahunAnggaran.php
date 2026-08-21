<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TahunAnggaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama', 'tanggal_mulai', 'tanggal_selesai', 'is_active',
        'status_approval', 'disetujui_oleh', 'disetujui_pada', 'catatan_penolakan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'is_active' => 'boolean',
            'disetujui_pada' => 'datetime',
        ];
    }

    public function kategoris()
    {
        return $this->hasMany(KategoriRapb::class);
    }

    public function penyetuju()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    /**
     * Setelah RAPB disetujui Ketua Umum, struktur anggaran (tahun anggaran itu sendiri,
     * kategori, sub-kategori, item rincian & rencana bulanan) TIDAK boleh diubah lagi
     * tanpa melalui pengajuan ulang — supaya persetujuan tidak jadi tidak bermakna
     * karena angka berubah diam-diam setelah disetujui. Realisasi (pencatatan
     * pengeluaran aktual) SENGAJA tidak dikunci oleh ini, karena realisasi memang
     * harus terus bisa dicatat sepanjang tahun anggaran berjalan.
     */
    public function isApprovalLocked(): bool
    {
        return $this->status_approval === 'Approved';
    }

    // Total rencana & realisasi lintas kategori — dipakai di Dashboard & LPJ
    public function totalRencana(): float
    {
        return ItemRincian::whereHas('subKategori.kategori', fn ($q) => $q->where('tahun_anggaran_id', $this->id))
            ->sum('jumlah_rencana');
    }

    public function totalRealisasi(): float
    {
        return Realisasi::whereHas('itemRincian.subKategori.kategori', fn ($q) => $q->where('tahun_anggaran_id', $this->id))
            ->sum('jumlah');
    }
}