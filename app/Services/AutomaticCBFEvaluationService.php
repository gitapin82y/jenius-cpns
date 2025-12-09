<?php

namespace App\Services;

use App\Models\AutomaticCBFEvaluation;
use App\Models\Recommendation;
use App\Models\Soal;
use App\Models\Material;

class AutomaticCBFEvaluationService
{
    /**
     * Evaluasi otomatis semua rekomendasi untuk user+set_soal tertentu
     */
    public function evaluateRecommendations(int $userId, int $setSoalId): array
    {
        // Ambil semua rekomendasi yang sudah di-generate
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

            // Count TP dan FP
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
            'precision_pct' => $precision * 100,
            'total' => count($results)
        ];
    }

    /**
     * Evaluasi 1 rekomendasi
     */
    private function evaluateSingleRecommendation(Recommendation $recommendation): array
    {
        $soal = $recommendation->soal;
        $material = $recommendation->material;

        // Extract keywords
        $soalKeywords = $soal->kata_kunci 
            ? json_decode($soal->kata_kunci, true) 
            : [];
        
        $materialKeywords = $material->kata_kunci 
            ? json_decode($material->kata_kunci, true) 
            : [];

        // Normalize keywords (lowercase)
        $soalKeywords = array_map('strtolower', array_map('trim', $soalKeywords));
        $materialKeywords = array_map('strtolower', array_map('trim', $materialKeywords));

        // Hitung intersection (irisan)
        $intersection = array_intersect($soalKeywords, $materialKeywords);
        $intersectionCount = count($intersection);

        // Ground Truth: Relevan jika ada minimal 1 kata kunci yang sama
        $isRelevant = $intersectionCount >= 1;

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
                'similarity_score' => $recommendation->similarity_score,
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
            'similarity_score' => $recommendation->similarity_score,
            'is_relevant' => $isRelevant,
            'classification' => $classification
        ];
    }
}