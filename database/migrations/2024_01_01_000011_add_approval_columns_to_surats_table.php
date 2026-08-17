<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surats', function (Blueprint $table) {
            $table->enum('status_approval', ['Pending', 'Approved', 'Rejected'])
                ->default('Pending')
                ->after('status');
            $table->foreignId('disetujui_oleh')->nullable()->after('status_approval')
                ->constrained('users')->nullOnDelete();
            $table->timestamp('disetujui_pada')->nullable()->after('disetujui_oleh');
            $table->text('catatan_penolakan')->nullable()->after('disetujui_pada');
        });
    }

    public function down(): void
    {
        Schema::table('surats', function (Blueprint $table) {
            $table->dropConstrainedForeignId('disetujui_oleh');
            $table->dropColumn(['status_approval', 'disetujui_pada', 'catatan_penolakan']);
        });
    }
};
