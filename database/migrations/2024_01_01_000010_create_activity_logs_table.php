<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('action');        // contoh: "create", "update", "delete", "approve", "reject", "reset_poin", "bulk_promosi"
            $table->string('subject_type');  // contoh: "App\Models\Santri"
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('description');   // ringkasan human-readable

            $table->json('data_sebelum')->nullable();
            $table->json('data_sesudah')->nullable();

            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index(['subject_type', 'subject_id']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
