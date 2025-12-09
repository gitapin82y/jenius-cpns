<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('automatic_cbf_evaluations', function (Blueprint $table) {
            // Tambah kolom threshold
            $table->float('threshold', 8, 4)->default(0.6)->after('similarity_score');
            
            // Tambah kolom apakah memenuhi threshold
            $table->boolean('meets_threshold')->default(false)->after('threshold');
        });
    }

    public function down(): void
    {
        Schema::table('automatic_cbf_evaluations', function (Blueprint $table) {
            $table->dropColumn(['threshold', 'meets_threshold']);
        });
    }
};