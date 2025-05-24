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