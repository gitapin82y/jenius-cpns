<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('automatic_cbf_evaluations', function (Blueprint $table) {
            // User manual feedback
            $table->boolean('user_feedback')->nullable()->after('classification');
            $table->timestamp('user_evaluated_at')->nullable()->after('user_feedback');
            
            // Final classification (prioritas manual > otomatis)
            $table->string('final_classification', 2)->nullable()->after('user_evaluated_at');
        });
    }

    public function down(): void
    {
        Schema::table('automatic_cbf_evaluations', function (Blueprint $table) {
            $table->dropColumn(['user_feedback', 'user_evaluated_at', 'final_classification']);
        });
    }
};