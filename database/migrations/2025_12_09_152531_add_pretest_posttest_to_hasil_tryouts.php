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
Schema::table('hasil_tryouts', function (Blueprint $table) {
            $table->enum('test_type', ['pretest', 'posttest', 'regular'])
                  ->default('regular')
                  ->after('set_soal_id');
            $table->foreignId('pretest_id')
                  ->nullable()
                  ->after('test_type')
                  ->constrained('hasil_tryouts')
                  ->onDelete('set null');
            $table->decimal('gain_score', 6, 2)
                  ->nullable()
                  ->after('pretest_id');
            $table->decimal('normalized_gain', 5, 4)
                  ->nullable()
                  ->after('gain_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
      Schema::table('hasil_tryouts', function (Blueprint $table) {
            $table->dropColumn([
                'test_type', 
                'pretest_id', 
                'gain_score', 
                'normalized_gain'
            ]);
        });
    }
};
