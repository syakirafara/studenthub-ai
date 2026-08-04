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
    Schema::create('opportunities', function (Blueprint $table) {
        $table->id();

        $table->string('judul');
        $table->string('penyelenggara')->nullable();
        $table->enum('kategori', ['lomba', 'beasiswa', 'magang']);
        $table->text('deskripsi')->nullable();
        $table->date('deadline')->nullable();

        $table->enum('biaya', ['gratis', 'berbayar', 'tidak_disebutkan'])
            ->default('tidak_disebutkan');
        $table->unsignedInteger('nominal_biaya')->nullable();

        $table->enum('tingkat', [
            'kampus', 'regional', 'nasional', 'internasional', 'tidak_disebutkan'
        ])->default('tidak_disebutkan');

        $table->string('link')->nullable();
        $table->string('poster_path')->nullable();
        $table->json('syarat')->nullable();

        $table->enum('status', ['menunggu', 'disetujui', 'ditolak'])
            ->default('menunggu');
        $table->text('catatan_admin')->nullable();

        $table->foreignId('submitted_by')->nullable()
            ->constrained('users')->nullOnDelete();
        $table->foreignId('verified_by')->nullable()
            ->constrained('users')->nullOnDelete();
        $table->timestamp('verified_at')->nullable();

        $table->timestamps();

        $table->index(['status', 'deadline']);
        $table->index('kategori');
    });
}

public function down(): void
{
    Schema::dropIfExists('opportunities');
}

};
