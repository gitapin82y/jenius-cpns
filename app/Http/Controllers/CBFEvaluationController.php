<?php

namespace App\Http\Controllers;

use App\Models\CBFEvaluation;
use App\Models\User;
use App\Models\RecommendationLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use DataTables;

class CBFEvaluationController extends Controller
{

public function dashboard()
{
    if (!Auth::user()->is_admin) {
        return redirect('/');
    }
    
    try {
        $stats = $this->calculateCBFMetrics();
        
        // Pastikan semua key yang dibutuhkan ada
        $requiredKeys = [
            'precision', 
            'total_evaluations', 
        ];
        
        foreach ($requiredKeys as $key) {
            if (!isset($stats[$key])) {
                $stats[$key] = 0;
            }
        }
        
        // Khusus untuk has_data, pastikan boolean
        if (!isset($stats['has_data'])) {
            $stats['has_data'] = false;
        }
        
        return view('admin.cbf-evaluation.dashboard', compact('stats'));
        
    } catch (\Exception $e) {
        // Default stats jika terjadi error
        $stats = [
 'precision' => 0, 
            'total_evaluations' => 0,
           
            'error_message' => $e->getMessage()
        ];
        
        return view('admin.cbf-evaluation.dashboard', compact('stats'));
    }
}

    public function getEvaluationData(Request $request)
    {
        if (!Auth::user()->is_admin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($request->ajax()) {
            $evaluations = CBFEvaluation::select(
                    'user_id',
                    DB::raw('MIN(id) as id'),
                    DB::raw('COUNT(*) as total_recommended'),
                    DB::raw('SUM(CASE WHEN user_feedback = 1 THEN 1 ELSE 0 END) as total_relevant'),
                    DB::raw('SUM(CASE WHEN user_feedback = 0 THEN 1 ELSE 0 END) as total_not_relevant'),
                    DB::raw('MAX(created_at) as latest_date')
                )
                ->where('evaluation_source', 'user')
                ->whereNotNull('user_feedback')
                ->whereHas('user', function ($query) {
        $query->where('is_cpns', true);
    })
                 ->groupBy('user_id')
                ->with('user')
    ->orderByRaw('MIN(created_at)')
    ->get();

            return DataTables::of($evaluations)
                ->addColumn('user_name', function ($evaluation) {
                    return $evaluation->user ? $evaluation->user->name : 'N/A';
                })
                ->addColumn('user_email', function ($evaluation) {
                    return $evaluation->user ? $evaluation->user->email : 'N/A';
                })
                ->addColumn('total_recommended', function ($evaluation) {
                    return $evaluation->total_recommended;
                })
                 ->addColumn('total_relevant', function ($evaluation) {
                    return $evaluation->total_relevant;
                })
               ->addColumn('total_not_relevant', function ($evaluation) {
                    return $evaluation->total_not_relevant;
                })
                ->addColumn('precision', function ($evaluation) {
                    $total = $evaluation->total_recommended;
                    $precision = $total > 0 ? ($evaluation->total_relevant / $total) * 100 : 0;
                    return number_format($precision, 2) . '%';
                })
                ->addColumn('action', function ($evaluation) {
                    $detailBtn = '<button type="button" class="btn btn-info btn-sm me-1" onclick="showEvaluationDetail(' . $evaluation->id . ')">
                        <i class="fas fa-eye"></i> Selengkapnya
                    </button>';
                    
                    $deleteBtn = '<button type="button" class="btn btn-danger btn-sm mt-1" onclick="deleteEvaluation(' . $evaluation->id . ', \'' . ($evaluation->user ? $evaluation->user->name : 'Unknown') . '\')">
                        <i class="fas fa-trash"></i> Hapus
                    </button>';
                    
                    return $detailBtn . $deleteBtn;
                })
                  ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function updateEvaluationDate(Request $request, $id)
    {
        if (!Auth::user()->is_admin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'created_at' => 'required|date'
        ]);

        try {

            
           CBFEvaluation::where('user_id', $id)
    ->update(['created_at' => $request->created_at]);


            return response()->json([
                'success' => true,
                'message' => 'Tanggal evaluasi berhasil diperbarui.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui tanggal evaluasi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function showEvaluationDetail($id)
    {
        if (!Auth::user()->is_admin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $evaluation = CBFEvaluation::with(['user', 'material', 'setSoal'])
                ->findOrFail($id);

            // Ambil semua evaluasi dari user yang sama untuk tryout yang sama
            $userEvaluations = CBFEvaluation::with(['material'])
                ->where('user_id', $evaluation->user_id)
                ->where('set_soal_id', $evaluation->set_soal_id)
                ->where('evaluation_source', 'user')
                ->whereNotNull('user_feedback')
                ->get();

            return response()->json([
                'success' => true,
                'evaluation' => $evaluation,
                'user_evaluations' => $userEvaluations,
                'total_evaluations' => $userEvaluations->count(),
                'relevant_count' => $userEvaluations->where('user_feedback', true)->count(),
                'not_relevant_count' => $userEvaluations->where('user_feedback', false)->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail evaluasi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteEvaluation($id)
    {
        if (!Auth::user()->is_admin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            DB::beginTransaction();

            $evaluation = CBFEvaluation::with(['user'])->findOrFail($id);
            $userId = $evaluation->user_id;
            $userName = $evaluation->user ? $evaluation->user->name : 'Unknown User';

            // Hapus semua evaluasi dari user ini (karena sistem one-time evaluation)
            $deletedCount = CBFEvaluation::where('user_id', $userId)->delete();

            // Reset is_review status user agar bisa memberikan evaluasi lagi
            if ($evaluation->user) {
                $evaluation->user->update(['is_review' => false]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Evaluasi dari {$userName} berhasil dihapus. Total {$deletedCount} record dihapus.",
                'deleted_count' => $deletedCount,
                'user_name' => $userName
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus evaluasi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkDeleteEvaluations(Request $request)
    {
        if (!Auth::user()->is_admin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'evaluation_ids' => 'required|array|min:1',
            'evaluation_ids.*' => 'exists:cbf_evaluations,id'
        ]);

        try {
            DB::beginTransaction();

            $evaluations = CBFEvaluation::with('user')
                ->whereIn('id', $request->evaluation_ids)
                ->get();

            $userIds = $evaluations->pluck('user_id')->unique();
            $deletedUsers = [];

            // Hapus evaluasi dan reset is_review untuk setiap user
            foreach ($userIds as $userId) {
                $user = User::find($userId);
                if ($user) {
                    $userEvaluationCount = CBFEvaluation::where('user_id', $userId)->count();
                    CBFEvaluation::where('user_id', $userId)->delete();
                    $user->update(['is_review' => false]);
                    
                    $deletedUsers[] = [
                        'name' => $user->name,
                        'email' => $user->email,
                        'evaluation_count' => $userEvaluationCount
                    ];
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Evaluasi berhasil dihapus untuk ' . count($deletedUsers) . ' pengguna.',
                'deleted_users' => $deletedUsers,
                'total_deleted' => $evaluations->count()
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus evaluasi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function resetUserReview(Request $request)
    {
        if (!Auth::user()->is_admin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        try {
            DB::beginTransaction();

            $user = User::findOrFail($request->user_id);
            
            // Hapus semua evaluasi user
            $deletedCount = CBFEvaluation::where('user_id', $user->id)->delete();
            
            // Reset is_review status
            $user->update(['is_review' => false]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Status review untuk {$user->name} berhasil direset. {$deletedCount} evaluasi dihapus.",
                'user_name' => $user->name,
                'deleted_count' => $deletedCount
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mereset status review: ' . $e->getMessage()
            ], 500);
        }
    }


public function calculateCBFMetrics()
{
    // Ambil evaluasi yang memiliki user_feedback atau expert_validation
    $evaluations = CBFEvaluation::where(function($query) {
        $query->whereNotNull('user_feedback')
              ->orWhereNotNull('expert_validation');
    })
    ->with(['user', 'material', 'setSoal'])
    ->get();

    $totalRelevant = CBFEvaluation::whereNotNull('user_feedback')
        ->where('user_feedback', true)
         ->whereHas('user', function ($query) {
        $query->where('is_cpns', true);
    })
        ->count();
    $totalNotRelevant = CBFEvaluation::whereNotNull('user_feedback')
        ->where('user_feedback', false)
         ->whereHas('user', function ($query) {
        $query->where('is_cpns', true);
    })
        ->count();

    // Hitung precision per pengguna untuk mendapatkan rata-rata precision
    $userPrecisions = CBFEvaluation::select(
            'user_id',
            DB::raw('SUM(CASE WHEN user_feedback = 1 THEN 1 ELSE 0 END) as relevant'),
            DB::raw('COUNT(*) as total')
        )
        ->whereNotNull('user_feedback')
        ->where('evaluation_source', 'user')
        ->whereHas('user', function ($query) {
        $query->where('is_cpns', true);
    })
        ->groupBy('user_id')
        ->get();

    $averagePrecision = $userPrecisions->map(function ($row) {
        return $row->total > 0 ? $row->relevant / $row->total : 0;
    })->avg() * 100;

    $totalUsersReviewed = User::where('is_review', true)->count();
    
    if ($evaluations->isEmpty()) {
        return [
            'tp' => 0, 'fp' => 0, 'tn' => 0, 'fn' => 0,
            'accuracy' => 0, 'precision' => 0, 'recall' => 0, 'f1_score' => 0,
            'total_evaluations' => 0,
            'pending_evaluations' => CBFEvaluation::whereNull('user_feedback')->whereNull('expert_validation')->count(),
            'user_evaluations' => CBFEvaluation::whereNotNull('user_feedback')->count(),
            'expert_evaluations' => CBFEvaluation::whereNotNull('expert_validation')->count(),
            'total_users_reviewed' => $totalUsersReviewed,
            'total_relevant_materials' => 0,
            'total_not_relevant_materials' => 0,
            'has_data' => false
        ];
    }
    
    // Gunakan final_relevance (expert validation priority over user feedback)
    $processedEvaluations = $evaluations->map(function($eval) {
        $eval->final_relevance = $eval->expert_validation ?? $eval->user_feedback;
        return $eval;
    });
    
    $tp = $processedEvaluations->where('is_recommended', true)->where('final_relevance', true)->count();
    $fp = $processedEvaluations->where('is_recommended', true)->where('final_relevance', false)->count();
    $tn = $processedEvaluations->where('is_recommended', false)->where('final_relevance', false)->count();
    $fn = $processedEvaluations->where('is_recommended', false)->where('final_relevance', true)->count();
    
    $total = $tp + $tn + $fp + $fn;
    $predictedPositive = $tp + $fp;
    $actualPositive = $tp + $fn;
    
    // Avoid division by zero
    $accuracy = $total > 0 ? ($tp + $tn) / $total : 0;
    $precision = $predictedPositive > 0 ? $tp / $predictedPositive : 0;
    $recall = $actualPositive > 0 ? $tp / $actualPositive : 0;
    $f1Score = ($precision + $recall) > 0 ? 2 * ($precision * $recall) / ($precision + $recall) : 0;
    
    return [
        'tp' => $tp, 'fp' => $fp, 'tn' => $tn, 'fn' => $fn,
        'accuracy' => round($accuracy * 100, 2),
        'precision' => round($averagePrecision, 2),
        'recall' => round($recall * 100, 2),
        'f1_score' => round($f1Score * 100, 2),
        'total_evaluations' => $evaluations->count(),
        'pending_evaluations' => CBFEvaluation::whereNull('user_feedback')->whereNull('expert_validation')->count(),
        'user_evaluations' => CBFEvaluation::whereNotNull('user_feedback')->count(),
        'expert_evaluations' => CBFEvaluation::whereNotNull('expert_validation')->count(),
        'total_relevant_materials' => $totalRelevant,
        'total_not_relevant_materials' => $totalNotRelevant,
        'has_data' => true
    ];
}
    
    public function expertEvaluation()
    {
        // Form untuk expert melakukan manual evaluation
        $pendingEvaluations = CBFEvaluation::whereNull('relevance_score')
            ->with(['user', 'material'])
            ->paginate(20);
            
        return view('admin.cbf-evaluation.expert-form', compact('pendingEvaluations'));
    }
    
    public function submitExpertEvaluation(Request $request)
    {
        $request->validate([
            'evaluation_id' => 'required|exists:cbf_evaluations,id',
            'relevance_score' => 'required|integer|min:1|max:5',
            'notes' => 'nullable|string'
        ]);
        
        CBFEvaluation::find($request->evaluation_id)->update([
            'relevance_score' => $request->relevance_score,
            'evaluation_type' => 'expert',
            'notes' => $request->notes
        ]);
        
        return response()->json(['message' => 'Evaluasi berhasil disimpan']);
    }
}