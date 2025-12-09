<?php
// app/Services/PretestPosttestService.php

namespace App\Services;

use App\Models\HasilTryout;
use App\Models\SetSoal;

class PretestPosttestService
{
    /**
     * Hitung Gain Score dan N-Gain
     */
    public function calculateGain(int $posttestId): array
    {
        $posttest = HasilTryout::findOrFail($posttestId);
        
        if (!$posttest->pretest_id) {
            throw new \Exception('Posttest harus punya pretest_id');
        }
        
        $pretest = HasilTryout::findOrFail($posttest->pretest_id);
        
        // Total score
        $pretestScore = $pretest->twk_score + $pretest->tiu_score + $pretest->tkp_score;
        $posttestScore = $posttest->twk_score + $posttest->tiu_score + $posttest->tkp_score;
        
        // Max score
        $setSoal = SetSoal::findOrFail($posttest->set_soal_id);
        $maxScore = $setSoal->soals()->count() * 5;
        
        // Gain Score
        $gainScore = $posttestScore - $pretestScore;
        
        // N-Gain
        $denominator = $maxScore - $pretestScore;
        $normalizedGain = $denominator > 0 ? ($gainScore / $denominator) : 0;
        
        // Kategori
        $category = $this->categorizeNGain($normalizedGain);
        
        // Simpan
        $posttest->update([
            'gain_score' => $gainScore,
            'normalized_gain' => $normalizedGain
        ]);
        
        return [
            'pretest_score' => $pretestScore,
            'posttest_score' => $posttestScore,
            'max_score' => $maxScore,
            'gain_score' => $gainScore,
            'normalized_gain' => $normalizedGain,
            'normalized_gain_pct' => $normalizedGain * 100,
            'category' => $category,
            'is_improved' => $gainScore > 0
        ];
    }
    
    private function categorizeNGain(float $nGain): string
    {
        if ($nGain < 0.3) return 'Rendah';
        if ($nGain <= 0.7) return 'Sedang';
        return 'Tinggi';
    }
    
    /**
     * Export untuk Python
     */
    public function exportForPython(): string
    {
        $posttests = HasilTryout::where('test_type', 'posttest')
            ->whereNotNull('pretest_id')
            ->with(['pretest', 'user'])
            ->get();
        
        $csv = "user_id,user_name,pretest_score,posttest_score,max_score,gain_score,normalized_gain,category\n";
        
        foreach ($posttests as $post) {
            $pretest = $post->pretest;
            
            $pretestScore = $pretest->twk_score + $pretest->tiu_score + $pretest->tkp_score;
            $posttestScore = $post->twk_score + $post->tiu_score + $post->tkp_score;
            
            $setSoal = SetSoal::find($post->set_soal_id);
            $maxScore = $setSoal ? $setSoal->soals()->count() * 5 : 0;
            
            $category = $this->categorizeNGain($post->normalized_gain);
            
            $csv .= sprintf(
                "%d,\"%s\",%d,%d,%d,%.2f,%.4f,%s\n",
                $post->user_id,
                str_replace('"', '""', $post->user ? $post->user->name : 'Unknown'),
                $pretestScore,
                $posttestScore,
                $maxScore,
                $post->gain_score,
                $post->normalized_gain,
                $category
            );
        }
        
        return $csv;
    }
}