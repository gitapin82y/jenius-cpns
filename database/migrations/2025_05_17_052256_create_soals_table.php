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
        Schema::create('soals', function (Blueprint $table) {
            $table->id();
            $table->integer('set_soal_id');
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
            $table->string('foto')->nullable();
            $table->text('pertanyaan');
            $table->text('jawaban_a');
            $table->integer('score_a')->nullable();
            $table->text('jawaban_b');
            $table->integer('score_b')->nullable();
            $table->text('jawaban_c');
            $table->integer('score_c')->nullable();
            $table->text('jawaban_d');
            $table->integer('score_d')->nullable();
            $table->text('jawaban_e');
            $table->integer('score_e')->nullable();
            $table->integer('poin')->default(5);
            $table->text('kata_kunci')->nullable();
            $table->enum('jawaban_benar', ['A', 'B', 'C', 'D', 'E'])->nullable();
            $table->text('pembahasan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soals');
    }
};
