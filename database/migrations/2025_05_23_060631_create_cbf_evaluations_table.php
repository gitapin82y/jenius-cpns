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
        Schema::create('cbf_evaluations', function (Blueprint $table) {
             $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('set_soal_id')->constrained('set_soals')->onDelete('cascade');
            $table->foreignId('material_id')->constrained('materials')->onDelete('cascade');
            $table->boolean('is_recommended'); // apakah materi direkomendasikan sistem
            $table->float('similarity_score', 8, 4)->nullable(); // cosine similarity score
            $table->boolean('user_feedback')->nullable(); // true = relevan, false = tidak relevan
            $table->text('user_comment')->nullable(); // komentar user (opsional)
            $table->boolean('expert_validation')->nullable(); // validasi dari admin/expert
            $table->enum('evaluation_source', ['user', 'expert', 'system'])->default('user');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cbf_evaluations');
    }
};
