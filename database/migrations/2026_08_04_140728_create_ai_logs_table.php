<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('ai_logs', function (Blueprint $table) {
        $table->id();

        $table->enum('jenis', ['ekstraksi_poster', 'skor_kecocokan']);
        $table->string('model');

        $table->foreignId('user_id')->nullable()
            ->constrained()->nullOnDelete();

        $table->unsignedInteger('token_masuk')->default(0);
        $table->unsignedInteger('token_keluar')->default(0);
        $table->unsignedInteger('durasi_ms')->default(0);

        $table->enum('status', ['berhasil', 'gagal', 'dari_cache']);
        $table->text('pesan_error')->nullable();

        $table->timestamps();

        $table->index(['jenis', 'created_at']);
    });
}

public function down(): void
{
    Schema::dropIfExists('ai_logs');
}

};
