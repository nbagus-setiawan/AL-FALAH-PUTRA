<?php

namespace Tests\Feature;

use App\Models\ItemRincian;
use App\Models\KategoriRapb;
use App\Models\Pelanggaran;
use App\Models\Santri;
use App\Models\SubKategoriRapb;
use App\Models\TahunAnggaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Test ini memverifikasi fix N+1 query (bukan test fitur/route) — memastikan jumlah
 * query yang dijalankan TIDAK bertambah proporsional dengan jumlah baris data.
 */
class QueryCountRegressionTest extends TestCase
{
    use RefreshDatabase;

    protected function queryCount(callable $callback): int
    {
        DB::flushQueryLog();
        DB::enableQueryLog();

        $callback();

        $count = count(DB::getQueryLog());
        DB::disableQueryLog();

        return $count;
    }

    public function test_total_poin_tidak_n_plus_1_saat_relasi_di_eager_load(): void
    {
        $pencatat = User::create([
            'name' => 'Sekretaris', 'username' => 'sek1', 'email' => 'sek1@afp.local',
            'password' => bcrypt('password'), 'role' => User::ROLE_SEKRETARIS, 'is_active' => true,
        ]);

        // Buat 8 santri, masing-masing dengan 2 catatan pelanggaran.
        foreach (range(1, 8) as $i) {
            $santri = Santri::create([
                'nis' => "N{$i}", 'nama_lengkap' => "Santri {$i}",
                'jenis_kelamin' => 'L', 'status' => Santri::STATUS_AKTIF,
            ]);

            Pelanggaran::create([
                'santri_id' => $santri->id, 'jenis_pelanggaran' => 'Terlambat',
                'kategori' => 'Ringan', 'tanggal' => now(), 'dicatat_oleh' => $pencatat->id,
            ]);
            Pelanggaran::create([
                'santri_id' => $santri->id, 'jenis_pelanggaran' => 'Bolos',
                'kategori' => 'Sedang', 'tanggal' => now(), 'dicatat_oleh' => $pencatat->id,
            ]);
        }

        $jumlahQuery = $this->queryCount(function () {
            Santri::aktif()->with(['pelanggarans', 'resetPoinHistory'])->get()
                ->sortByDesc(fn (Santri $s) => $s->total_poin)
                ->values();
        });

        // 1 query utama santri + 1 eager-load pelanggarans + 1 eager-load resetPoinHistory
        // = 3 query, TIDAK PEDULI berapa banyak santri/pelanggaran (bukan proporsional / N+1).
        $this->assertLessThanOrEqual(3, $jumlahQuery, "Diduga N+1: {$jumlahQuery} query untuk 8 santri, seharusnya cukup 3 query tetap.");
    }

    public function test_total_poin_di_cache_per_instance_tidak_query_dobel(): void
    {
        $santri = Santri::create([
            'nis' => 'CACHE1', 'nama_lengkap' => 'Santri Cache',
            'jenis_kelamin' => 'L', 'status' => Santri::STATUS_AKTIF,
        ]);

        $jumlahQuery = $this->queryCount(function () use ($santri) {
            // Akses total_poin lalu warna_poin (yang secara internal memanggil total_poin lagi) —
            // harus tetap query yang sama (fallback tanpa eager load), bukan dobel.
            $s = Santri::find($santri->id);
            $s->total_poin;
            $s->warna_poin;
        });

        // find() santri (1) + fallback total_poin: pelanggarans sum (1) + resetPoinHistory (1) = 3.
        // Kalau caching TIDAK jalan, warna_poin akan menambah 2 query lagi jadi 5.
        $this->assertLessThanOrEqual(3, $jumlahQuery, "Caching total_poin tidak berjalan — query dobel saat warna_poin diakses ({$jumlahQuery} query).");
    }

    public function test_total_realisasi_tidak_n_plus_1_saat_realisasis_di_eager_load(): void
    {
        $pencatat = User::create([
            'name' => 'Bendahara', 'username' => 'bendahara1', 'email' => 'bendahara1@afp.local',
            'password' => bcrypt('password'), 'role' => User::ROLE_BENDAHARA, 'is_active' => true,
        ]);

        $tahunAnggaran = TahunAnggaran::create([
            'nama' => '2026/2027', 'tanggal_mulai' => '2026-07-01', 'tanggal_selesai' => '2027-06-30',
            'is_active' => true, 'status_approval' => 'Pending',
        ]);
        $kategori = KategoriRapb::create(['tahun_anggaran_id' => $tahunAnggaran->id, 'nama' => 'K', 'jenis' => 'Pengeluaran']);
        $subKategori = SubKategoriRapb::create(['kategori_rapb_id' => $kategori->id, 'nama' => 'SK']);

        // 6 item rincian, masing-masing dengan 2 realisasi.
        foreach (range(1, 6) as $i) {
            $item = ItemRincian::create([
                'sub_kategori_rapb_id' => $subKategori->id, 'nama' => "Item {$i}", 'jumlah_rencana' => 1000000,
            ]);
            $item->realisasis()->create(['jumlah' => 100000, 'tanggal' => now(), 'dicatat_oleh' => $pencatat->id]);
            $item->realisasis()->create(['jumlah' => 200000, 'tanggal' => now(), 'dicatat_oleh' => $pencatat->id]);
        }

        $jumlahQuery = $this->queryCount(function () use ($subKategori) {
            $subKategori->load('itemRincians.realisasis');
            $subKategori->itemRincians->each(function (ItemRincian $item) {
                // Memicu ketiga accessor yang saling bergantung pada total_realisasi.
                $item->total_realisasi;
                $item->selisih;
                $item->persentase_serapan;
            });
        });

        // 1 query itemRincians + 1 eager-load realisasis = 2 query tetap, tidak peduli jumlah item.
        $this->assertLessThanOrEqual(2, $jumlahQuery, "Diduga N+1 pada ItemRincian::total_realisasi: {$jumlahQuery} query untuk 6 item.");
    }
}
