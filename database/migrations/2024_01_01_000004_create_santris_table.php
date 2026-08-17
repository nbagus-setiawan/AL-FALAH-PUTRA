<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('santris', function (Blueprint $table) {
            $table->id();

            // Identitas utama — NIS adalah identifier unik di seluruh sistem
            $table->string('nis')->unique();
            $table->string('nisn')->nullable(); // data pelengkap, bukan identifier utama
            $table->string('nama_lengkap');
            $table->string('nama_panggilan')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('foto_path')->nullable();

            // Alamat
            $table->text('alamat')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('kabupaten_kota')->nullable();

            // Data orang tua / wali
            $table->string('nama_ayah')->nullable();
            $table->string('pekerjaan_ayah')->nullable();
            $table->string('nama_ibu')->nullable();
            $table->string('pekerjaan_ibu')->nullable();
            $table->string('nama_wali')->nullable();
            $table->string('kontak_wali')->nullable();

            // Kontak darurat
            $table->string('kontak_darurat_nama')->nullable();
            $table->string('kontak_darurat_hubungan')->nullable();
            $table->string('kontak_darurat_telepon')->nullable();

            // Data sensitif — akses dibatasi hanya untuk role Sekretaris (dienforce di level aplikasi/policy)
            $table->string('nik')->nullable();
            $table->text('riwayat_kesehatan')->nullable();
            $table->text('alergi')->nullable();
            $table->string('golongan_darah', 3)->nullable();

            // Data akademik & relasi
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->nullOnDelete();

            // Status santri — independen dari status perizinan
            $table->enum('status', ['Aktif', 'Lulus', 'Boyong'])->default('Aktif');
            $table->date('tanggal_masuk')->nullable();
            $table->date('tanggal_keluar')->nullable(); // diisi saat Lulus/Boyong
            $table->text('keterangan_keluar')->nullable(); // alasan boyong, dsb

            $table->timestamps();

            $table->index('status');
        });

        // Riwayat prestasi santri — tabel terpisah agar bisa banyak entri per santri
        Schema::create('prestasi_santris', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santris')->cascadeOnDelete();
            $table->string('nama_prestasi');
            $table->string('tingkat')->nullable(); // contoh: Kabupaten, Provinsi, Nasional
            $table->date('tanggal');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prestasi_santris');
        Schema::dropIfExists('santris');
    }
};
