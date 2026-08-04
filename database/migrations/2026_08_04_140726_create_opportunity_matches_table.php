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
    Schema::create('opportunity_matches', function (Blueprint $table) {
        $table->id();

        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->foreignId('opportunity_id')->constrained()->cascadeOnDelete();

        $table->unsignedTinyInteger('skor');
        $table->json('terpenuhi')->nullable();
        $table->json('belum_terpenuhi')->nullable();
        $table->text('saran')->nullable();
        $table->timestamp('dihitung_pada')->nullable();

        $table->timestamps();

        $table->unique(['user_id', 'opportunity_id']);
        $table->index(['user_id', 'skor']);
    });
}

public function down(): void
{
    Schema::dropIfExists('opportunity_matches');
}

};
