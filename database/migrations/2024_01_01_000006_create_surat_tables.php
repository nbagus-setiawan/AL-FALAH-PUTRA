<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_surats', function (Blueprint $table) {
            $table->id();
            $table->string('nama');        // contoh: "Surat Undangan"
            $table->string('kode', 10)->unique(); // contoh: "UND"
            $table->timestamps();
        });

        Schema::create('surats', function (Blueprint $table) {
            $table->id();

            // Nomor surat lengkap, contoh: 012/AFP/UND/VIII/2026 — hasil generate, disimpan agar tidak perlu dihitung ulang
            $table->string('nomor_surat')->unique();
            $table->unsignedInteger('nomor_urut'); // bagian urut saja, untuk query & validasi kontinuitas
            $table->unsignedSmallInteger('tahun');  // tahun penomoran (untuk reset per 1 Januari)

            $table->foreignId('jenis_surat_id')->constrained('jenis_surats');
            $table->enum('arah', ['Masuk', 'Keluar']); // surat masuk vs keluar
            $table->date('tanggal');
            $table->string('perihal');
            $table->string('tujuan_pengirim'); // tujuan (jika keluar) / pengirim (jika masuk)
            $table->longText('isi_ringkas')->nullable();
            $table->longText('isi_lengkap')->nullable(); // hasil rich text editor, untuk surat keluar dari Generator
            $table->string('lampiran_path')->nullable();
            $table->string('file_pdf_path')->nullable();

            $table->foreignId('santri_id')->nullable()->constrained('santris')->nullOnDelete(); // opsional, jika surat terkait santri

            $table->enum('status', ['Draft', 'Terkirim', 'Diarsipkan'])->default('Draft');

            $table->foreignId('dibuat_oleh')->constrained('users');
            $table->timestamps();

            $table->unique(['tahun', 'nomor_urut']); // pastikan tidak ada nomor urut ganda dalam 1 tahun
            $table->index(['jenis_surat_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surats');
        Schema::dropIfExists('jenis_surats');
    }
};
