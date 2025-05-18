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
        Schema::create('set_soals', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('kategori', ['Tryout', 'Latihan']);
            $table->enum('status', ['Draf', 'Publish'])->default('Draf');
            $table->integer('jumlah_soal')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('set_soals');
    }
};
