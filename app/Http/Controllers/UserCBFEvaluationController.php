<?php
// app/Http/Controllers/UserCBFEvaluationController.php

namespace App\Http\Controllers;

use App\Models\CBFEvaluation;
use App\Models\SetSoal;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\CBFEvaluationService;
use App\Models\AutomaticCBFEvaluation;

class UserCBFEvaluationController extends Controller
{
    private $cbfEvaluationService;
    
    public function __construct(CBFEvaluationService $cbfEvaluationService)
    {
        $this->cbfEvaluationService = $cbfEvaluationService;
    }
    public function submitEvaluation(Request $request)
    {
        $request->validate([
            'set_soal_id' => 'required|exists:set_soals,id',
            'evaluations' => 'required|array|min:1',
            'evaluations.*.material_id' => 'required|exists:materials,id',
            'evaluations.*.user_feedback' => 'required|boolean',
            'evaluations.*.similarity_score' => 'required|numeric|min:0|max:1',
            'user_comment' => 'nullable|string|max:1000'
        ]);
        
          try {
            DB::beginTransaction();

             $user = Auth::user();
            
            // Check for existing evaluation
            $existingEvaluation = CBFEvaluation::where('user_id', $user->id)
                ->where('set_soal_id', $request->set_soal_id)
                ->exists();
                
            if ($existingEvaluation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah memberikan penilaian sebelumnya.'
                ], 400);
            }

        if ($user->is_review) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah pernah memberikan penilaian sebelumnya. Setiap pengguna hanya dapat memberikan penilaian satu kali untuk penelitian ini.',
                'error_code' => 'ALREADY_REVIEWED'
            ], 400);
        }
        
        $user->update(['is_review' => true]);

            // Create evaluations using service
            $evaluations = $this->cbfEvaluationService->createEvaluationsFromRecommendation(
                Auth::id(),
                $request->set_soal_id,
                $request->evaluations,
                $request->user_comment
            );


            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Evaluasi berhasil disimpan',
                'total_created' => count($evaluations)
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

     public function submitFeedback(Request $request)
    {
        $request->validate([
            'evaluation_id' => 'required|exists:automatic_cbf_evaluations,id',
            'is_relevant' => 'required|boolean'
        ]);

        try {
            $evaluation = AutomaticCBFEvaluation::findOrFail($request->evaluation_id);

            // Validasi: user hanya bisa nilai rekomendasi miliknya sendiri
            if ($evaluation->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk menilai rekomendasi ini.'
                ], 403);
            }

            // Validasi: sudah dinilai sebelumnya (sekali nilai)
            if ($evaluation->user_feedback !== null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah memberikan penilaian untuk rekomendasi ini.'
                ], 400);
            }

            // Update dengan penilaian user
            $evaluation->update([
                'user_feedback' => $request->is_relevant,
                'user_evaluated_at' => now(),
                'final_classification' => $request->is_relevant ? 'TP' : 'FP'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Terima kasih atas penilaian Anda!',
                'data' => [
                    'user_feedback' => $evaluation->user_feedback,
                    'final_classification' => $evaluation->final_classification,
                    'evaluated_at' => $evaluation->user_evaluated_at->format('Y-m-d H:i:s')
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

      /**
     * Get statistik penilaian user
     */
    public function getUserStats($userId, $setSoalId)
    {
        $stats = AutomaticCBFEvaluation::where('user_id', $userId)
            ->where('set_soal_id', $setSoalId)
            ->selectRaw('
                COUNT(*) as total_recommendations,
                SUM(CASE WHEN user_feedback IS NOT NULL THEN 1 ELSE 0 END) as total_evaluated,
                SUM(CASE WHEN user_feedback = 1 THEN 1 ELSE 0 END) as user_relevant,
                SUM(CASE WHEN user_feedback = 0 THEN 1 ELSE 0 END) as user_not_relevant
            ')
            ->first();

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
    
    public function getUserEvaluationStats()
    {
        $userId = Auth::id();
        
        $stats = [
            'total_evaluations' => CBFEvaluation::where('user_id', $userId)->count(),
            'relevant_count' => CBFEvaluation::where('user_id', $userId)->where('user_feedback', true)->count(),
            'not_relevant_count' => CBFEvaluation::where('user_id', $userId)->where('user_feedback', false)->count(),
        ];
        
        return response()->json($stats);
    }
}