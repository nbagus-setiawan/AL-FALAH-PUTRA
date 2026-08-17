<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tingkat: Ula / Wustho / Ulya, dst — bisa disesuaikan per pondok
        Schema::create('tingkats', function (Blueprint $table) {
            $table->id();
            $table->string('nama');       // contoh: "Ula"
            $table->unsignedTinyInteger('urutan'); // untuk menentukan urutan naik tingkat
            $table->timestamps();
        });

        // Kelas per tingkat per tahun ajaran
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tingkat_id')->constrained('tingkats')->cascadeOnDelete();
            $table->string('nama');           // contoh: "Ula 1A"
            $table->string('tahun_ajaran');   // contoh: "2026/2027"
            $table->timestamps();

            $table->unique(['tingkat_id', 'nama', 'tahun_ajaran']);
        });

        // Asrama
        Schema::create('asramas', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->unsignedInteger('kapasitas');
            $table->foreignId('penanggung_jawab_id')->nullable()->constrained('pengurus')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asramas');
        Schema::dropIfExists('kelas');
        Schema::dropIfExists('tingkats');
    }
};
