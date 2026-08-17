<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pelanggarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santris')->cascadeOnDelete();

            $table->string('jenis_pelanggaran'); // deskripsi bebas, contoh: "Terlambat sholat berjamaah"
            $table->enum('kategori', ['Ringan', 'Sedang', 'Berat']); // menentukan poin (10/20/30)
            $table->unsignedTinyInteger('poin'); // diisi otomatis dari kategori saat simpan, tapi disimpan eksplisit untuk histori
            $table->date('tanggal');
            $table->text('sanksi')->nullable();
            $table->text('catatan')->nullable();

            $table->foreignId('dicatat_oleh')->constrained('users');
            $table->timestamps();

            $table->index(['santri_id', 'tanggal']);
        });

        // Menyimpan histori reset poin per santri (agar total poin bisa dihitung ulang dari titik reset terakhir)
        Schema::create('reset_poin_santris', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santris')->cascadeOnDelete();
            $table->foreignId('direset_oleh')->constrained('users');
            $table->text('alasan')->nullable();
            $table->timestamp('direset_pada');
            $table->timestamps();

            $table->index(['santri_id', 'direset_pada']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reset_poin_santris');
        Schema::dropIfExists('pelanggarans');
    }
};
