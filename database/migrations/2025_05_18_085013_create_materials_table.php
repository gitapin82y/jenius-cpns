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
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
             $table->string('title');
            $table->enum('kategori', ['TWK', 'TIU', 'TKP']);
            $table->enum('tipe', [
                'Nasionalisme',
                'Integritas',
                'Bela Negara',
                'Pilar Negara',
                'Bahasa Indonesia',
                'Verbal (Analogi)',
                'Verbal (Silogisme)',
                'Verbal (Analisis)',
                'Numerik (Hitung Cepat)',
                'Numerik (Deret Angka)',
                'Numerik (Perbandingan Kuantitatif)',
                'Numerik (Soal Cerita)',
                'Figural (Analogi)',
                'Figural (Ketidaksamaan)',
                'Figural (Serial)',
                'Pelayanan Publik',
                'Jejaring Kerja',
                'Sosial Budaya',
                'Teknologi Informasi dan Komunikasi (TIK)',
                'Profesionalisme',
                'Anti Radikalisme',
            ]);
            $table->longText('content');
            $table->enum('status', ['Draf', 'Publish'])->default('Draf');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
