<?php

namespace Tests\Feature\Console;

use App\Mail\IzinTerlambatNotification;
use App\Models\Izin;
use App\Models\Santri;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class UpdateStatusIzinTerlambatTest extends TestCase
{
    use RefreshDatabase;

    protected function buatSantri(string $nis): Santri
    {
        return Santri::create([
            'nis' => $nis,
            'nama_lengkap' => "Santri {$nis}",
            'jenis_kelamin' => 'L',
            'status' => Santri::STATUS_AKTIF,
        ]);
    }

    protected function buatIzinSedangIzin(Santri $santri, User $pengaju, string $rencanaKembali): Izin
    {
        return Izin::create([
            'santri_id' => $santri->id,
            'jenis_izin' => 'Kepentingan',
            'tanggal_keluar' => now()->subDays(5)->toDateString(),
            'rencana_kembali' => $rencanaKembali,
            'alasan' => 'Keperluan keluarga',
            'penjemput' => 'Wali Santri',
            'kontak_penjemput' => '08123456789',
            'status' => Izin::STATUS_SEDANG_IZIN,
            'diajukan_oleh' => $pengaju->id,
            'notifikasi_telat_terkirim' => false,
        ]);
    }

    public function test_command_menandai_terlambat_dan_mengirim_notifikasi(): void
    {
        Mail::fake();

        $sekretaris = User::create([
            'name' => 'Sekretaris', 'username' => 'sek1', 'email' => 'sek1@afp.local',
            'password' => bcrypt('password'), 'role' => User::ROLE_SEKRETARIS, 'is_active' => true,
        ]);
        $santri = $this->buatSantri('S001');
        $izin = $this->buatIzinSedangIzin($santri, $sekretaris, now()->subDay()->toDateString());

        $this->artisan('izin:update-terlambat')->assertExitCode(0);

        $izin->refresh();
        $this->assertSame(Izin::STATUS_TERLAMBAT, $izin->status);
        $this->assertTrue($izin->notifikasi_telat_terkirim);
        Mail::assertSent(IzinTerlambatNotification::class, 1);
    }

    /**
     * FIX inti: sebelumnya scope hanya menyaring status == 'Sedang Izin', sehingga
     * izin yang statusnya SUDAH 'Terlambat' tapi notifikasi_telat_terkirim masih
     * false (mis. karena percobaan sebelumnya gagal kirim email) TIDAK PERNAH
     * terjaring lagi -> notifikasi hilang permanen. Test ini membuktikan izin
     * semacam itu sekarang tetap terjaring dan dicoba dikirim ulang.
     */
    public function test_izin_berstatus_terlambat_yang_belum_ternotifikasi_tetap_terjaring_ulang(): void
    {
        Mail::fake();

        $sekretaris = User::create([
            'name' => 'Sekretaris', 'username' => 'sek1', 'email' => 'sek1@afp.local',
            'password' => bcrypt('password'), 'role' => User::ROLE_SEKRETARIS, 'is_active' => true,
        ]);
        $santri = $this->buatSantri('S002');

        // Simulasikan izin yang sudah gagal dinotifikasi kemarin: status sudah
        // 'Terlambat' tapi notifikasi_telat_terkirim masih false.
        $izin = Izin::create([
            'santri_id' => $santri->id,
            'jenis_izin' => 'Sakit',
            'tanggal_keluar' => now()->subDays(5)->toDateString(),
            'rencana_kembali' => now()->subDays(2)->toDateString(),
            'alasan' => 'Sakit',
            'penjemput' => 'Wali Santri',
            'kontak_penjemput' => '08123456789',
            'status' => Izin::STATUS_TERLAMBAT,
            'diajukan_oleh' => $sekretaris->id,
            'notifikasi_telat_terkirim' => false,
        ]);

        $this->assertTrue(Izin::terlambatBelumNotif()->whereKey($izin->id)->exists());

        $this->artisan('izin:update-terlambat')->assertExitCode(0);

        $izin->refresh();
        $this->assertTrue($izin->notifikasi_telat_terkirim);
        Mail::assertSent(IzinTerlambatNotification::class, 1);
    }

    public function test_izin_sudah_ternotifikasi_tidak_terjaring_lagi(): void
    {
        Mail::fake();

        $sekretaris = User::create([
            'name' => 'Sekretaris', 'username' => 'sek1', 'email' => 'sek1@afp.local',
            'password' => bcrypt('password'), 'role' => User::ROLE_SEKRETARIS, 'is_active' => true,
        ]);
        $santri = $this->buatSantri('S003');
        $izin = Izin::create([
            'santri_id' => $santri->id,
            'jenis_izin' => 'Sakit',
            'tanggal_keluar' => now()->subDays(5)->toDateString(),
            'rencana_kembali' => now()->subDays(2)->toDateString(),
            'alasan' => 'Sakit',
            'penjemput' => 'Wali Santri',
            'kontak_penjemput' => '08123456789',
            'status' => Izin::STATUS_TERLAMBAT,
            'diajukan_oleh' => $sekretaris->id,
            'notifikasi_telat_terkirim' => true,
        ]);

        $this->artisan('izin:update-terlambat')->assertExitCode(0);

        Mail::assertNothingSent();
        $izin->refresh();
        $this->assertTrue($izin->notifikasi_telat_terkirim);
    }
}
