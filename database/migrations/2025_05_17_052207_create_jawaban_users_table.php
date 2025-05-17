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
        Schema::create('jawaban_users', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('soal_id');
            $table->integer('set_soal_id');
            $table->enum('jawaban_user', ['A', 'B', 'C', 'D', 'E'])->nullable();
            $table->enum('status', ['kosong', 'benar', 'salah']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jawaban_users');
    }
};
