<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('izins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santris')->cascadeOnDelete();

            $table->enum('jenis_izin', ['Sakit', 'Kepentingan']);
            $table->date('tanggal_keluar');
            $table->date('rencana_kembali');
            $table->date('tanggal_kembali_aktual')->nullable();
            $table->text('alasan');
            $table->string('penjemput');
            $table->string('kontak_penjemput');

            // Pending -> Approved/Rejected (oleh Ketua Umum) -> Sedang Izin -> Sudah Kembali (atau Terlambat)
            $table->enum('status', [
                'Pending', 'Rejected', 'Approved', 'Sedang Izin', 'Sudah Kembali', 'Terlambat',
            ])->default('Pending');

            $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('disetujui_pada')->nullable();
            $table->text('catatan_penolakan')->nullable();

            $table->foreignId('surat_id')->nullable()->constrained('surats')->nullOnDelete(); // link ke Surat Izin yang di-generate
            $table->foreignId('diajukan_oleh')->constrained('users');

            $table->boolean('notifikasi_telat_terkirim')->default(false); // cegah kirim email berulang

            $table->timestamps();

            $table->index(['santri_id', 'status']);
            $table->index('rencana_kembali');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('izins');
    }
};
