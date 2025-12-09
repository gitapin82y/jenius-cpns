<?php

namespace App\Http\Controllers;

use App\Models\AutomaticCBFEvaluation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use DataTables;

class AutomaticCBFEvaluationController extends Controller
{
    /**
     * Dashboard evaluasi otomatis
     */
    public function dashboard()
    {
        if (!Auth::user()->is_admin) {
            return redirect('/');
        }

        try {
            $stats = $this->calculateMetrics();
            
            return view('admin.automatic-cbf-evaluation.dashboard', compact('stats'));
            
        } catch (\Exception $e) {
            $stats = [
                'total_evaluations' => 0,
                'total_users' => 0,
                'total_tp' => 0,
                'total_fp' => 0,
                'average_precision' => 0,
                'has_data' => false,
                'error_message' => $e->getMessage()
            ];
            
            return view('admin.automatic-cbf-evaluation.dashboard', compact('stats'));
        }
    }

    /**
     * Hitung metrics untuk dashboard
     */
    private function calculateMetrics()
    {
        $evaluations = AutomaticCBFEvaluation::whereHas('user', function($query) {
            $query->where('is_cpns', true);
        })->get();

        if ($evaluations->isEmpty()) {
            return [
                'total_evaluations' => 0,
                'total_users' => 0,
                'total_tp' => 0,
                'total_fp' => 0,
                'average_precision' => 0,
                'has_data' => false
            ];
        }

        $totalTP = $evaluations->where('classification', 'TP')->count();
        $totalFP = $evaluations->where('classification', 'FP')->count();
        
        // Hitung precision per user
        $userPrecisions = AutomaticCBFEvaluation::select(
                'user_id',
                DB::raw('SUM(CASE WHEN classification = "TP" THEN 1 ELSE 0 END) as tp'),
                DB::raw('COUNT(*) as total')
            )
            ->whereHas('user', function($query) {
                $query->where('is_cpns', true);
            })
            ->groupBy('user_id')
            ->get();

        $avgPrecision = $userPrecisions->map(function($row) {
            return $row->total > 0 ? ($row->tp / $row->total) : 0;
        })->avg();

        $totalUsers = $userPrecisions->count();

        return [
            'total_evaluations' => $evaluations->count(),
            'total_users' => $totalUsers,
            'total_tp' => $totalTP,
            'total_fp' => $totalFP,
            'average_precision' => round($avgPrecision * 100, 2),
            'has_data' => true
        ];
    }

    /**
     * DataTable untuk list user + precision
     */
    public function getUserPrecisionData(Request $request)
    {
        if (!Auth::user()->is_admin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($request->ajax()) {
            $userStats = DB::table('automatic_cbf_evaluations')
                ->join('users', 'automatic_cbf_evaluations.user_id', '=', 'users.id')
                ->select(
                    'automatic_cbf_evaluations.user_id',
                    DB::raw('MIN(automatic_cbf_evaluations.id) as id'),
                    'users.name as user_name',
                    'users.email as user_email',
                    DB::raw('COUNT(*) as total_recommendations'),
                    DB::raw('SUM(CASE WHEN classification = "TP" THEN 1 ELSE 0 END) as tp'),
                    DB::raw('SUM(CASE WHEN classification = "FP" THEN 1 ELSE 0 END) as fp'),
                    DB::raw('ROUND((SUM(CASE WHEN classification = "TP" THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) as precision'),
                    DB::raw('MAX(automatic_cbf_evaluations.created_at) as latest_date')
                )
                ->where('users.is_cpns', true)
                ->groupBy('automatic_cbf_evaluations.user_id', 'users.name', 'users.email')
                ->orderBy('users.name')
                ->get();

            return DataTables::of($userStats)
                ->addColumn('action', function($row) {
                    return '
                        <button type="button" class="btn btn-info btn-sm" onclick="showUserDetail(' . $row->user_id . ')">
                            <i class="fas fa-eye"></i> Detail
                        </button>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    /**
     * Detail evaluasi per user
     */
    public function getUserDetail($userId)
    {
        if (!Auth::user()->is_admin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $user = User::findOrFail($userId);
            
            $evaluations = AutomaticCBFEvaluation::with(['setSoal', 'soal', 'material'])
                ->where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->get();

            $stats = [
                'total' => $evaluations->count(),
                'tp' => $evaluations->where('classification', 'TP')->count(),
                'fp' => $evaluations->where('classification', 'FP')->count(),
            ];
            
            $stats['precision'] = $stats['total'] > 0 
                ? round(($stats['tp'] / $stats['total']) * 100, 2) 
                : 0;

            return response()->json([
                'success' => true,
                'user' => $user,
                'stats' => $stats,
                'evaluations' => $evaluations
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail: ' . $e->getMessage()
            ], 500);
        }
    }
}