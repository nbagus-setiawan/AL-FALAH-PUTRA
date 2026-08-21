<?php

namespace Tests\Feature\Sekretaris;

use App\Models\ActivityLog;
use App\Models\Kelas;
use App\Models\Santri;
use App\Models\Tingkat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KenaikanKelasProsesTest extends TestCase
{
    use RefreshDatabase;

    protected User $sekretaris;

    protected Kelas $kelasAsal;

    protected Kelas $kelasTujuan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sekretaris = User::create([
            'name' => 'Sekretaris Test',
            'username' => 'sekretaris_test',
            'email' => 'sekretaris_test@afp.local',
            'password' => bcrypt('password'),
            'role' => User::ROLE_SEKRETARIS,
            'is_active' => true,
        ]);

        $tingkatAsal = Tingkat::create(['nama' => 'Ula', 'urutan' => 1]);
        $tingkatTujuan = Tingkat::create(['nama' => 'Wustho', 'urutan' => 2]);

        $this->kelasAsal = Kelas::create(['tingkat_id' => $tingkatAsal->id, 'nama' => 'A', 'tahun_ajaran' => '2026/2027']);
        $this->kelasTujuan = Kelas::create(['tingkat_id' => $tingkatTujuan->id, 'nama' => 'A', 'tahun_ajaran' => '2027/2028']);
    }

    protected function buatSantri(string $nis): Santri
    {
        return Santri::create([
            'nis' => $nis,
            'nama_lengkap' => "Santri {$nis}",
            'jenis_kelamin' => 'L',
            'kelas_id' => $this->kelasAsal->id,
            'status' => Santri::STATUS_AKTIF,
        ]);
    }

    public function test_jumlah_diproses_sesuai_dengan_baris_yang_benar_benar_berubah(): void
    {
        $santri1 = $this->buatSantri('S001');
        $santri2 = $this->buatSantri('S002');

        $response = $this->actingAs($this->sekretaris)->post(route('sekretaris.kenaikan-kelas.proses'), [
            'promosi' => [
                $santri1->id => $this->kelasTujuan->id,
                $santri2->id => $this->kelasTujuan->id,
            ],
        ]);

        $response->assertRedirect(route('sekretaris.kenaikan-kelas.preview'));

        $this->assertDatabaseHas('santris', ['id' => $santri1->id, 'kelas_id' => $this->kelasTujuan->id]);
        $this->assertDatabaseHas('santris', ['id' => $santri2->id, 'kelas_id' => $this->kelasTujuan->id]);

        $log = ActivityLog::where('action', 'bulk_promosi')->latest()->first();
        $this->assertNotNull($log);
        $this->assertSame(2, $log->data_sesudah['jumlah_diproses']);
    }

    public function test_santri_id_tidak_valid_pada_promosi_ditolak_dan_tidak_mempengaruhi_hitungan(): void
    {
        $santri1 = $this->buatSantri('S001');
        $idTidakValid = $santri1->id + 9999; // dipastikan tidak ada di tabel santris

        $response = $this->actingAs($this->sekretaris)->post(route('sekretaris.kenaikan-kelas.proses'), [
            'promosi' => [
                $santri1->id => $this->kelasTujuan->id,
                $idTidakValid => $this->kelasTujuan->id,
            ],
        ]);

        // FIX: request ditolak (422) karena ada santri_id yang tidak valid di antara key
        // "promosi", bukan diam-diam lolos dengan hitungan yang salah.
        $response->assertStatus(422);
        $this->assertDatabaseHas('santris', ['id' => $santri1->id, 'kelas_id' => $this->kelasAsal->id]);
    }

    public function test_lulus_menandai_status_dan_ikut_terhitung_benar(): void
    {
        $santriLulus = $this->buatSantri('S010');

        $response = $this->actingAs($this->sekretaris)->post(route('sekretaris.kenaikan-kelas.proses'), [
            'lulus' => [$santriLulus->id],
        ]);

        $response->assertRedirect(route('sekretaris.kenaikan-kelas.preview'));

        $this->assertDatabaseHas('santris', [
            'id' => $santriLulus->id,
            'status' => Santri::STATUS_LULUS,
        ]);

        $log = ActivityLog::where('action', 'bulk_promosi')->latest()->first();
        $this->assertSame(1, $log->data_sesudah['jumlah_diproses']);
    }
}
