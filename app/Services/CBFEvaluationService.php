<?php
// app/Services/CBFEvaluationService.php

namespace App\Services;

use App\Models\CBFEvaluation;
use App\Models\RecommendationLog;
use App\Models\Material;

class CBFEvaluationService
{
    /**
     * Create CBF evaluations based on recommendation log and user feedback
     */
    public function createEvaluationsFromRecommendation(
        int $userId, 
        int $setSoalId, 
        array $userFeedbacks, 
        string $userComment = null
    ): array {
        
        // 1. Get recommendation log
        $recommendationLog = RecommendationLog::where('user_id', $userId)
            ->where('set_soal_id', $setSoalId)
            ->latest()
            ->first();
            
        if (!$recommendationLog) {
            throw new \Exception('Recommendation log not found');
        }
        
        $evaluationsCreated = [];
        
        // 2. Create evaluations for RECOMMENDED materials
        foreach ($recommendationLog->recommendations as $kategori => $items) {
            foreach ($items as $item) {
                $materialId = $item['material']['id'];
                
                // Find user feedback for this material
                $userFeedback = collect($userFeedbacks)
                    ->firstWhere('material_id', $materialId);
                
                $evaluation = CBFEvaluation::create([
                    'user_id' => $userId,
                    'set_soal_id' => $setSoalId,
                    'material_id' => $materialId,
                    'is_recommended' => true,
                    'similarity_score' => $item['similarity'],
                    'user_feedback' => $userFeedback ? $userFeedback['user_feedback'] : null,
                    'user_comment' => $userComment,
                    'evaluation_source' => 'user'
                ]);
                
                $evaluationsCreated[] = $evaluation;
            }
        }
        
        // 3. Create evaluations for NOT RECOMMENDED materials (for TN/FN calculation)
        $recommendedMaterialIds = collect($evaluationsCreated)->pluck('material_id');
        
        $notRecommendedMaterials = Material::where('status', 'Publish')
            ->whereNotIn('id', $recommendedMaterialIds)
            ->inRandomOrder()
            ->limit(min(count($evaluationsCreated), 20)) // Balance the dataset
            ->get();
            
        foreach ($notRecommendedMaterials as $material) {
            $evaluation = CBFEvaluation::create([
                'user_id' => $userId,
                'set_soal_id' => $setSoalId,
                'material_id' => $material->id,
                'is_recommended' => false,
                'similarity_score' => 0,
                'user_feedback' => null, // To be filled by expert
                'evaluation_source' => 'system'
            ]);
            
            $evaluationsCreated[] = $evaluation;
        }
        
        return $evaluationsCreated;
    }
    
    /**
     * Calculate confusion matrix metrics
     */
    public function calculateMetrics(): array
    {
        $evaluations = CBFEvaluation::where(function($query) {
            $query->whereNotNull('user_feedback')
                  ->orWhereNotNull('expert_validation');
        })->get();
        
        if ($evaluations->isEmpty()) {
            return $this->getEmptyMetrics();
        }
        
        // Use expert validation if available, otherwise user feedback
        $processedEvaluations = $evaluations->map(function($eval) {
            $eval->final_relevance = $eval->expert_validation ?? $eval->user_feedback;
            return $eval;
        });
        
        $tp = $processedEvaluations->where('is_recommended', true)->where('final_relevance', true)->count();
        $fp = $processedEvaluations->where('is_recommended', true)->where('final_relevance', false)->count();
        $tn = $processedEvaluations->where('is_recommended', false)->where('final_relevance', false)->count();
        $fn = $processedEvaluations->where('is_recommended', false)->where('final_relevance', true)->count();
        
        return $this->calculateMetricsFromConfusionMatrix($tp, $fp, $tn, $fn, $evaluations->count());
    }
    
    private function getEmptyMetrics(): array
    {
        return [
            'tp' => 0, 'fp' => 0, 'tn' => 0, 'fn' => 0,
            'accuracy' => 0, 'precision' => 0, 'recall' => 0, 'f1_score' => 0,
            'total_evaluations' => 0, 'has_data' => false
        ];
    }
    
    private function calculateMetricsFromConfusionMatrix($tp, $fp, $tn, $fn, $totalEvaluations): array
    {
        $total = $tp + $tn + $fp + $fn;
        $predictedPositive = $tp + $fp;
        $actualPositive = $tp + $fn;
        
        $accuracy = $total > 0 ? ($tp + $tn) / $total : 0;
        $precision = $predictedPositive > 0 ? $tp / $predictedPositive : 0;
        $recall = $actualPositive > 0 ? $tp / $actualPositive : 0;
        $f1Score = ($precision + $recall) > 0 ? 2 * ($precision * $recall) / ($precision + $recall) : 0;
        
        return [
            'tp' => $tp, 'fp' => $fp, 'tn' => $tn, 'fn' => $fn,
            'accuracy' => round($accuracy * 100, 2),
            'precision' => round($precision * 100, 2),
            'recall' => round($recall * 100, 2),
            'f1_score' => round($f1Score * 100, 2),
            'total_evaluations' => $totalEvaluations,
            'has_data' => true
        ];
    }
}