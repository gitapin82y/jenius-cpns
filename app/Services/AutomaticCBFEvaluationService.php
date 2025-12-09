<?php

namespace App\Services;

use App\Models\AutomaticCBFEvaluation;
use App\Models\Recommendation;
use App\Models\Soal;
use App\Models\Material;

class AutomaticCBFEvaluationService
{

    const MINIMUM_SIMILARITY_THRESHOLD = 0.6;


    public function evaluateRecommendations(int $userId, int $setSoalId): array
    {
        $recommendations = Recommendation::where('user_id', $userId)
            ->where('set_soal_id', $setSoalId)
            ->with(['soal', 'material'])
            ->get();

        $results = [];
        $tp = 0;
        $fp = 0;

        foreach ($recommendations as $recommendation) {
            $evaluation = $this->evaluateSingleRecommendation($recommendation);
            $results[] = $evaluation;

            if ($evaluation['classification'] === 'TP') {
                $tp++;
            } else {
                $fp++;
            }
        }

        $precision = ($tp + $fp) > 0 ? ($tp / ($tp + $fp)) : 0;

        return [
            'evaluations' => $results,
            'tp' => $tp,
            'fp' => $fp,
            'precision' => $precision,
            'precision_pct' => round($precision * 100, 2),
            'total' => count($results),
            'threshold' => self::MINIMUM_SIMILARITY_THRESHOLD
        ];
    }

    /**
     * ✅ Evaluasi 1 rekomendasi berdasarkan THRESHOLD SAJA
     */
    private function evaluateSingleRecommendation(Recommendation $recommendation): array
    {
        $soal = $recommendation->soal;
        $material = $recommendation->material;

        // Extract keywords (untuk informasi saja, tidak untuk evaluasi)
        $soalKeywords = $soal->kata_kunci 
            ? json_decode($soal->kata_kunci, true) 
            : [];
        
        $materialKeywords = $material->kata_kunci 
            ? json_decode($material->kata_kunci, true) 
            : [];

        // Normalize keywords
        $soalKeywords = array_map('strtolower', array_map('trim', $soalKeywords));
        $materialKeywords = array_map('strtolower', array_map('trim', $materialKeywords));

        // Hitung intersection (untuk informasi saja)
        $intersection = array_intersect($soalKeywords, $materialKeywords);
        $intersectionCount = count($intersection);

        // Similarity score
        $similarityScore = $recommendation->similarity_score ?? 0;

        // ✅ EVALUASI BERDASARKAN THRESHOLD SAJA
        // RELEVAN jika similarity >= 0.6
        // TIDAK RELEVAN jika similarity < 0.6
        $isRelevant = $similarityScore >= self::MINIMUM_SIMILARITY_THRESHOLD;

        // Classification
        $classification = $isRelevant ? 'TP' : 'FP';

        // Simpan ke database
        $evaluation = AutomaticCBFEvaluation::updateOrCreate(
            [
                'user_id' => $recommendation->user_id,
                'set_soal_id' => $recommendation->set_soal_id,
                'soal_id' => $recommendation->soal_id,
                'material_id' => $recommendation->material_id
            ],
            [
                'soal_keywords' => $soalKeywords,
                'material_keywords' => $materialKeywords,
                'intersection_keywords' => array_values($intersection),
                'intersection_count' => $intersectionCount,
                'similarity_score' => $similarityScore,
                'threshold' => self::MINIMUM_SIMILARITY_THRESHOLD,
                'meets_threshold' => $isRelevant,
                'is_relevant' => $isRelevant,
                'is_recommended' => true,
                'classification' => $classification
            ]
        );

        return [
            'soal_id' => $soal->id,
            'soal_text' => substr($soal->pertanyaan, 0, 100) . '...',
            'material_id' => $material->id,
            'material_title' => $material->title,
            'soal_keywords' => $soalKeywords,
            'material_keywords' => $materialKeywords,
            'intersection' => array_values($intersection),
            'intersection_count' => $intersectionCount,
            'similarity_score' => $similarityScore,
            'threshold' => self::MINIMUM_SIMILARITY_THRESHOLD,
            'meets_threshold' => $isRelevant,
            'is_relevant' => $isRelevant,
            'classification' => $classification
        ];
    }
    
}