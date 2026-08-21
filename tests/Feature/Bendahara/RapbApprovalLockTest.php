<?php

namespace Tests\Feature\Bendahara;

use App\Models\ItemRincian;
use App\Models\KategoriRapb;
use App\Models\SubKategoriRapb;
use App\Models\TahunAnggaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RapbApprovalLockTest extends TestCase
{
    use RefreshDatabase;

    protected User $bendahara;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bendahara = User::create([
            'name' => 'Bendahara Test',
            'username' => 'bendahara_test',
            'email' => 'bendahara_test@afp.local',
            'password' => bcrypt('password'),
            'role' => User::ROLE_BENDAHARA,
            'is_active' => true,
        ]);
    }

    protected function buatTahunAnggaran(string $statusApproval): TahunAnggaran
    {
        return TahunAnggaran::create([
            'nama' => '2026/2027',
            'tanggal_mulai' => '2026-07-01',
            'tanggal_selesai' => '2027-06-30',
            'is_active' => true,
            'status_approval' => $statusApproval,
        ]);
    }

    public function test_bendahara_bisa_menambah_kategori_saat_status_pending(): void
    {
        $tahunAnggaran = $this->buatTahunAnggaran('Pending');

        $response = $this->actingAs($this->bendahara)->post(
            route('bendahara.tahun-anggaran.kategori.store', $tahunAnggaran),
            ['nama' => 'Iuran Santri', 'jenis' => 'Pemasukan']
        );

        $response->assertRedirect(route('bendahara.tahun-anggaran.kategori.index', $tahunAnggaran));
        $this->assertDatabaseHas('kategori_rapbs', [
            'tahun_anggaran_id' => $tahunAnggaran->id,
            'nama' => 'Iuran Santri',
        ]);
    }

    public function test_bendahara_tidak_bisa_menambah_kategori_saat_status_approved(): void
    {
        $tahunAnggaran = $this->buatTahunAnggaran('Approved');

        $response = $this->actingAs($this->bendahara)->post(
            route('bendahara.tahun-anggaran.kategori.store', $tahunAnggaran),
            ['nama' => 'Iuran Santri', 'jenis' => 'Pemasukan']
        );

        $response->assertStatus(422);
        $this->assertDatabaseMissing('kategori_rapbs', [
            'tahun_anggaran_id' => $tahunAnggaran->id,
            'nama' => 'Iuran Santri',
        ]);
    }

    public function test_bendahara_tidak_bisa_mengubah_kategori_saat_rapb_approved(): void
    {
        $tahunAnggaran = $this->buatTahunAnggaran('Approved');
        $kategori = KategoriRapb::create([
            'tahun_anggaran_id' => $tahunAnggaran->id,
            'nama' => 'Kategori Awal',
            'jenis' => 'Pengeluaran',
        ]);

        $response = $this->actingAs($this->bendahara)->put(
            route('bendahara.kategori.update', $kategori),
            ['nama' => 'Kategori Diubah', 'jenis' => 'Pengeluaran']
        );

        $response->assertStatus(422);
        $this->assertDatabaseHas('kategori_rapbs', ['id' => $kategori->id, 'nama' => 'Kategori Awal']);
    }

    public function test_bendahara_tidak_bisa_menghapus_kategori_saat_rapb_approved(): void
    {
        $tahunAnggaran = $this->buatTahunAnggaran('Approved');
        $kategori = KategoriRapb::create([
            'tahun_anggaran_id' => $tahunAnggaran->id,
            'nama' => 'Kategori Awal',
            'jenis' => 'Pengeluaran',
        ]);

        $response = $this->actingAs($this->bendahara)->delete(route('bendahara.kategori.destroy', $kategori));

        $response->assertStatus(422);
        $this->assertDatabaseHas('kategori_rapbs', ['id' => $kategori->id]);
    }

    public function test_bendahara_tidak_bisa_menambah_sub_kategori_saat_rapb_approved(): void
    {
        $tahunAnggaran = $this->buatTahunAnggaran('Approved');
        $kategori = KategoriRapb::create([
            'tahun_anggaran_id' => $tahunAnggaran->id,
            'nama' => 'Kategori',
            'jenis' => 'Pengeluaran',
        ]);

        $response = $this->actingAs($this->bendahara)->post(
            route('bendahara.sub-kategori.store', $kategori),
            ['nama' => 'Sub Kategori Baru']
        );

        $response->assertStatus(422);
        $this->assertDatabaseMissing('sub_kategori_rapbs', ['nama' => 'Sub Kategori Baru']);
    }

    public function test_bendahara_tidak_bisa_menambah_item_rincian_saat_rapb_approved(): void
    {
        $tahunAnggaran = $this->buatTahunAnggaran('Approved');
        $kategori = KategoriRapb::create([
            'tahun_anggaran_id' => $tahunAnggaran->id,
            'nama' => 'Kategori',
            'jenis' => 'Pengeluaran',
        ]);
        $subKategori = SubKategoriRapb::create(['kategori_rapb_id' => $kategori->id, 'nama' => 'Sub Kategori']);

        $response = $this->actingAs($this->bendahara)->post(route('bendahara.item-rincian.store'), [
            'sub_kategori_rapb_id' => $subKategori->id,
            'nama' => 'Item Baru',
            'jumlah_rencana' => 1000000,
        ]);

        $response->assertStatus(422);
        $this->assertDatabaseMissing('item_rincians', ['nama' => 'Item Baru']);
    }

    public function test_tahun_anggaran_tidak_bisa_diedit_setelah_approved(): void
    {
        $tahunAnggaran = $this->buatTahunAnggaran('Approved');

        $response = $this->actingAs($this->bendahara)->put(
            route('bendahara.tahun-anggaran.update', $tahunAnggaran),
            [
                'nama' => '2027/2028',
                'tanggal_mulai' => '2026-07-01',
                'tanggal_selesai' => '2027-06-30',
            ]
        );

        $response->assertStatus(422);
        $this->assertDatabaseHas('tahun_anggarans', ['id' => $tahunAnggaran->id, 'nama' => '2026/2027']);
    }

    public function test_tahun_anggaran_bisa_diedit_saat_masih_pending(): void
    {
        $tahunAnggaran = $this->buatTahunAnggaran('Pending');

        $response = $this->actingAs($this->bendahara)->put(
            route('bendahara.tahun-anggaran.update', $tahunAnggaran),
            [
                'nama' => '2027/2028',
                'tanggal_mulai' => '2026-07-01',
                'tanggal_selesai' => '2027-06-30',
            ]
        );

        $response->assertRedirect(route('bendahara.tahun-anggaran.show', $tahunAnggaran));
        $this->assertDatabaseHas('tahun_anggarans', ['id' => $tahunAnggaran->id, 'nama' => '2027/2028']);
    }

    /**
     * Realisasi (pencatatan pengeluaran aktual) SENGAJA tidak ikut dikunci —
     * ini harus tetap bisa dicatat kapan pun sepanjang tahun anggaran berjalan,
     * berbeda dari struktur rencana anggaran (kategori/sub-kategori/item rincian).
     */
    public function test_realisasi_tetap_bisa_dicatat_walau_rapb_sudah_approved(): void
    {
        Storage::fake('private');

        $tahunAnggaran = $this->buatTahunAnggaran('Approved');
        $kategori = KategoriRapb::create([
            'tahun_anggaran_id' => $tahunAnggaran->id,
            'nama' => 'Kategori',
            'jenis' => 'Pengeluaran',
        ]);
        $subKategori = SubKategoriRapb::create(['kategori_rapb_id' => $kategori->id, 'nama' => 'Sub Kategori']);
        $item = ItemRincian::create([
            'sub_kategori_rapb_id' => $subKategori->id,
            'nama' => 'Item',
            'jumlah_rencana' => 1000000,
        ]);

        $response = $this->actingAs($this->bendahara)->post(route('bendahara.realisasi.store', $item), [
            'jumlah' => 500000,
            'tanggal' => now()->toDateString(),
            'bukti' => UploadedFile::fake()->create('bukti.pdf', 100, 'application/pdf'),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('realisasis', ['item_rincian_id' => $item->id, 'jumlah' => 500000]);
    }
}
