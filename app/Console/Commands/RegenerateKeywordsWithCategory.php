<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Soal;
use App\Models\Material;
use App\Services\KeywordExtractionService;

class RegenerateKeywordsWithCategory extends Command
{
    /**
     * Signature command
     */
    protected $signature = 'keywords:regenerate {--type=all : Type to regenerate (all/soal/material)}';

    /**
     * Description command
     */
    protected $description = 'Regenerate keywords dengan menambahkan kategori (TWK/TIU/TKP)';

    private $keywordService;

    /**
     * Constructor
     */
    public function __construct(KeywordExtractionService $keywordService)
    {
        parent::__construct();
        $this->keywordService = $keywordService;
    }

    /**
     * Execute command
     */
    public function handle()
    {
        $type = $this->option('type');

        $this->info('');
        $this->info('🚀 Starting keyword regeneration...');
        $this->info('');

        if ($type === 'all' || $type === 'soal') {
            $this->regenerateSoalKeywords();
        }

        if ($type === 'all' || $type === 'material') {
            $this->regenerateMaterialKeywords();
        }

        $this->info('');
        $this->info('✅ Regenerasi keywords selesai!');
        $this->info('');

        return 0;
    }

    /**
     * Regenerate keywords untuk Soal
     */
    private function regenerateSoalKeywords()
    {
        $this->info('🔄 Regenerating SOAL keywords...');
        
        $soals = Soal::all();
        
        if ($soals->isEmpty()) {
            $this->warn('⚠️  Tidak ada soal ditemukan.');
            return;
        }

        $bar = $this->output->createProgressBar($soals->count());
        $bar->start();

        $updated = 0;
        $failed = 0;

        foreach ($soals as $soal) {
            try {
                // ✅ Extract keywords dengan kategori
                $keywords = $this->keywordService->extractKeywords(
                    $soal->pertanyaan,
                    $soal->tipe,
                    $soal->kategori  // ← TAMBAHKAN KATEGORI
                );

                $soal->update([
                    'kata_kunci' => json_encode($keywords)
                ]);

                $updated++;
            } catch (\Exception $e) {
                $failed++;
                $this->error("\n❌ Error pada soal ID {$soal->id}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("✅ {$updated} soal keywords berhasil diupdate");
        
        if ($failed > 0) {
            $this->warn("⚠️  {$failed} soal gagal diupdate");
        }
        
        $this->newLine();
    }

    /**
     * Regenerate keywords untuk Material
     */
    private function regenerateMaterialKeywords()
    {
        $this->info('🔄 Regenerating MATERIAL keywords...');
        
        $materials = Material::all();
        
        if ($materials->isEmpty()) {
            $this->warn('⚠️  Tidak ada material ditemukan.');
            return;
        }

        $bar = $this->output->createProgressBar($materials->count());
        $bar->start();

        $updated = 0;
        $failed = 0;

        foreach ($materials as $material) {
            try {
                // ✅ Extract keywords dengan kategori
                $keywords = $this->keywordService->extractKeywords(
                    $material->title,
                    $material->tipe,
                    $material->kategori  // ← TAMBAHKAN KATEGORI
                );

                $material->update([
                    'kata_kunci' => json_encode($keywords)
                ]);

                $updated++;
            } catch (\Exception $e) {
                $failed++;
                $this->error("\n❌ Error pada material ID {$material->id}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("✅ {$updated} material keywords berhasil diupdate");
        
        if ($failed > 0) {
            $this->warn("⚠️  {$failed} material gagal diupdate");
        }
        
        $this->newLine();
    }
}