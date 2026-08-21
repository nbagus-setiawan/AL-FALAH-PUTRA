<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AsramaPenghuni extends Model
{
    use HasFactory;

    protected $table = 'asrama_penghuni';

    protected $fillable = ['santri_id', 'asrama_id', 'tanggal_masuk', 'tanggal_keluar', 'keterangan'];

    protected function casts(): array
    {
        return [
            'tanggal_masuk' => 'date',
            'tanggal_keluar' => 'date',
        ];
    }

    public function santri()
    {
        return $this->belongsTo(Santri::class);
    }

    public function asrama()
    {
        return $this->belongsTo(Asrama::class);
    }

    /**
     * Pindahkan santri ke asrama baru.
     * Riwayat lama TIDAK dihapus/ditimpa — hanya diberi tanggal_keluar,
     * lalu baris baru dibuat untuk asrama tujuan.
     *
     * FIX: dibungkus DB::transaction(). Sebelumnya "tutup baris lama" dan "buat baris
     * baru" adalah 2 query terpisah tanpa transaction — kalau proses terputus di
     * antara keduanya (error, request timeout, dsb), santri bisa berakhir tanpa
     * asrama aktif sama sekali (baris lama sudah ditutup, baris baru belum sempat
     * dibuat). Dengan transaction, kalau salah satu gagal, keduanya di-rollback
     * bersama sehingga data tidak pernah berada di kondisi setengah jalan.
     */
    public static function pindahkan(Santri $santri, Asrama $asramaBaru, ?string $tanggal = null, ?string $keterangan = null): self
    {
        $tanggal ??= now()->toDateString();

        return DB::transaction(function () use ($santri, $asramaBaru, $tanggal, $keterangan) {
            static::where('santri_id', $santri->id)
                ->whereNull('tanggal_keluar')
                ->update(['tanggal_keluar' => $tanggal]);

            return static::create([
                'santri_id' => $santri->id,
                'asrama_id' => $asramaBaru->id,
                'tanggal_masuk' => $tanggal,
                'keterangan' => $keterangan,
            ]);
        });
    }
}