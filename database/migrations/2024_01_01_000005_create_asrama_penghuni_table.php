<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Riwayat penghuni asrama — TIDAK ditimpa saat santri pindah,
        // baris baru dibuat setiap kali ada perpindahan asrama
        Schema::create('asrama_penghuni', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santris')->cascadeOnDelete();
            $table->foreignId('asrama_id')->constrained('asramas')->cascadeOnDelete();
            $table->date('tanggal_masuk');
            $table->date('tanggal_keluar')->nullable(); // null = masih tinggal di asrama ini saat ini
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index(['santri_id', 'tanggal_keluar']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asrama_penghuni');
    }
};
