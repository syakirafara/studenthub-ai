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
        Schema::create('extraction_reviews', function (Blueprint $table) {
            $table->id();

            $table->foreignId('opportunity_id')->constrained()->cascadeOnDelete();

            $table->json('hasil_ai');
            $table->json('hasil_final')->nullable();
            $table->unsignedTinyInteger('jumlah_koreksi')->default(0);
            $table->json('field_dikoreksi')->nullable();

            $table->foreignId('reviewed_by')->nullable()
                ->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('extraction_reviews');
    }
};
