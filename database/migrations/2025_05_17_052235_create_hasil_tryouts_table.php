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
        Schema::create('hasil_tryouts', function (Blueprint $table) {
             $table->id();
            $table->integer('user_id');
            $table->integer('set_soal_id');
            $table->integer('twk_score');
            $table->integer('tiu_score');
            $table->integer('tkp_score');
            $table->integer('total_benar');
            $table->integer('total_salah');
            $table->integer('total_kosong');
            // tipe
            // $table->integer('nasionalisme');
            // $table->integer('integritas');
            // $table->integer('bela_negara');
            // $table->integer('pilar_negara');
            // $table->integer('bahasa_indonesia');
            // $table->integer('verbal_analogi');
            // $table->integer('verbal_silogisme');
            // $table->integer('verbal_analisis');
            // $table->integer('numerik_hitung_cepat');
            // $table->integer('numerik_deret_angka');
            // $table->integer('numerik_perbandingan_kuantitatif');
            // $table->integer('numerik_soal_cerita');
            // $table->integer('figural_analogi');
            // $table->integer('figural_ketidaksamaan');
            // $table->integer('figural_serial');
            // $table->integer('pelayanan_publik');
            // $table->integer('jejaring_kerja');
            // $table->integer('sosial_budaya');
            // $table->integer('teknologi_informasi_dan_komunikasi_tik');
            // $table->integer('profesionalisme');
            // $table->integer('anti_radikalisme');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hasil_tryouts');
    }
};
