<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tahun_anggarans', function (Blueprint $table) {
            $table->id();
            $table->string('nama'); // contoh: "2026/2027"
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->boolean('is_active')->default(false); // tahun anggaran yang sedang berjalan
            $table->timestamps();
        });

        Schema::create('kategori_rapbs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_anggaran_id')->constrained('tahun_anggarans')->cascadeOnDelete();
            $table->string('nama');
            $table->enum('jenis', ['Pemasukan', 'Pengeluaran']);
            $table->timestamps();
        });

        Schema::create('sub_kategori_rapbs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_rapb_id')->constrained('kategori_rapbs')->cascadeOnDelete();
            $table->string('nama'); // bebas dibuat Bendahara
            $table->timestamps();
        });

        Schema::create('item_rincians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sub_kategori_rapb_id')->constrained('sub_kategori_rapbs')->cascadeOnDelete();
            $table->string('nama');
            $table->decimal('jumlah_rencana', 15, 2)->default(0); // total rencana (bisa jadi total dari rencana_bulanans)
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // Rencana anggaran per bulan — opsional, mendukung "rencana bisa dipecah per bulan"
        Schema::create('rencana_bulanans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_rincian_id')->constrained('item_rincians')->cascadeOnDelete();
            $table->unsignedTinyInteger('bulan'); // 1-12
            $table->decimal('jumlah', 15, 2);
            $table->timestamps();

            $table->unique(['item_rincian_id', 'bulan']);
        });

        Schema::create('realisasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_rincian_id')->constrained('item_rincians')->cascadeOnDelete();
            $table->decimal('jumlah', 15, 2);
            $table->date('tanggal');
            $table->string('bukti_path')->nullable(); // upload bukti, storage privat
            $table->text('keterangan')->nullable();
            $table->foreignId('dicatat_oleh')->constrained('users');
            $table->timestamps();
        });

        // Approval RAPB oleh Ketua Umum — per tahun anggaran (bisa diperluas ke per kategori jika perlu)
        Schema::table('tahun_anggarans', function (Blueprint $table) {
            $table->enum('status_approval', ['Pending', 'Approved', 'Rejected'])->default('Pending')->after('is_active');
            $table->foreignId('disetujui_oleh')->nullable()->after('status_approval')->constrained('users')->nullOnDelete();
            $table->timestamp('disetujui_pada')->nullable()->after('disetujui_oleh');
            $table->text('catatan_penolakan')->nullable()->after('disetujui_pada');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('realisasis');
        Schema::dropIfExists('rencana_bulanans');
        Schema::dropIfExists('item_rincians');
        Schema::dropIfExists('sub_kategori_rapbs');
        Schema::dropIfExists('kategori_rapbs');
        Schema::dropIfExists('tahun_anggarans');
    }
};
