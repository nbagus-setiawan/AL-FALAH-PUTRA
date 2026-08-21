<?php

namespace Tests\Feature\Sekretaris;

use App\Models\Asrama;
use App\Models\AsramaPenghuni;
use App\Models\Santri;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AsramaPindahSantriTest extends TestCase
{
    use RefreshDatabase;

    protected User $sekretaris;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sekretaris = User::create([
            'name' => 'Sekretaris Test', 'username' => 'sekretaris_asrama', 'email' => 'sekretaris_asrama@afp.local',
            'password' => bcrypt('password'), 'role' => User::ROLE_SEKRETARIS, 'is_active' => true,
        ]);
    }

    protected function buatSantri(string $nis): Santri
    {
        return Santri::create([
            'nis' => $nis, 'nama_lengkap' => "Santri {$nis}",
            'jenis_kelamin' => 'L', 'status' => Santri::STATUS_AKTIF,
        ]);
    }

    public function test_pindah_asrama_menutup_riwayat_lama_dan_membuat_baris_baru(): void
    {
        $asramaLama = Asrama::create(['nama' => 'Asrama Lama', 'kapasitas' => 10]);
        $asramaBaru = Asrama::create(['nama' => 'Asrama Baru', 'kapasitas' => 10]);
        $santri = $this->buatSantri('S001');

        $penghuniLama = AsramaPenghuni::create([
            'santri_id' => $santri->id,
            'asrama_id' => $asramaLama->id,
            'tanggal_masuk' => now()->subMonths(2)->toDateString(),
        ]);

        $response = $this->actingAs($this->sekretaris)->post(
            route('sekretaris.santri.pindah-asrama', $santri),
            ['asrama_id' => $asramaBaru->id, 'keterangan' => 'Pindah rutin']
        );

        $response->assertRedirect();

        $this->assertNotNull($penghuniLama->fresh()->tanggal_keluar);
        $this->assertDatabaseHas('asrama_penghuni', [
            'santri_id' => $santri->id,
            'asrama_id' => $asramaBaru->id,
            'tanggal_keluar' => null,
        ]);
    }

    public function test_pindah_asrama_ditolak_jika_kapasitas_penuh(): void
    {
        $asramaPenuh = Asrama::create(['nama' => 'Asrama Penuh', 'kapasitas' => 1]);
        $santriPenghuni = $this->buatSantri('S010');
        AsramaPenghuni::create([
            'santri_id' => $santriPenghuni->id,
            'asrama_id' => $asramaPenuh->id,
            'tanggal_masuk' => now()->subMonth()->toDateString(),
        ]);

        $santriBaru = $this->buatSantri('S011');

        $response = $this->actingAs($this->sekretaris)->post(
            route('sekretaris.santri.pindah-asrama', $santriBaru),
            ['asrama_id' => $asramaPenuh->id]
        );

        $response->assertStatus(422);
        $this->assertDatabaseMissing('asrama_penghuni', [
            'santri_id' => $santriBaru->id,
            'asrama_id' => $asramaPenuh->id,
        ]);
    }

    public function test_pindahkan_method_bersifat_atomic_lewat_transaction(): void
    {
        $asramaBaru = Asrama::create(['nama' => 'Asrama Baru', 'kapasitas' => 5]);
        $santri = $this->buatSantri('S020');

        $hasil = AsramaPenghuni::pindahkan($santri, $asramaBaru, now()->toDateString(), 'Test langsung');

        $this->assertInstanceOf(AsramaPenghuni::class, $hasil);
        $this->assertDatabaseHas('asrama_penghuni', [
            'id' => $hasil->id,
            'santri_id' => $santri->id,
            'asrama_id' => $asramaBaru->id,
            'tanggal_keluar' => null,
        ]);
    }
}
