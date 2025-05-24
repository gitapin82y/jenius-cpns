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
        Schema::create('recommendation_logs', function (Blueprint $table) {
                $table->id();
    $table->integer('user_id');
    $table->integer('set_soal_id');
    $table->json('recommendations'); // store all recommendations
    $table->json('debug_info'); // store CBF process info
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recommendation_logs');
    }
};
