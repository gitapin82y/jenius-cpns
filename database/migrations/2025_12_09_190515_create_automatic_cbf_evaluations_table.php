<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('automatic_cbf_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('set_soal_id')->constrained('set_soals')->onDelete('cascade');
            $table->foreignId('soal_id')->constrained('soals')->onDelete('cascade');
            $table->foreignId('material_id')->constrained('materials')->onDelete('cascade');
            
            // Kata kunci untuk ground truth
            $table->json('soal_keywords'); // Kata kunci soal
            $table->json('material_keywords'); // Kata kunci materi
            $table->json('intersection_keywords')->nullable(); // Irisan kata kunci
            $table->integer('intersection_count')->default(0); // Jumlah kata kunci yang sama
            
            // Metrics
            $table->float('similarity_score', 8, 4); // Cosine similarity
            $table->boolean('is_relevant'); // Ground truth: TRUE jika intersection_count >= 1
            
            // Classification (untuk confusion matrix)
            $table->boolean('is_recommended')->default(true); // Selalu true karena ini rekomendasi
            $table->enum('classification', ['TP', 'FP']); // TP jika relevant, FP jika tidak
            
            $table->timestamps();
            
            // Index untuk performa
            $table->index(['user_id', 'set_soal_id']);
            $table->index('is_relevant');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('automatic_cbf_evaluations');
    }
};