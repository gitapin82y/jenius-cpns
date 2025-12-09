<?php
// app/Http/Controllers/ExportController.php

namespace App\Http\Controllers;

use App\Models\CBFEvaluation;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\HasilTryout;

class ExportController extends Controller
{
    /**
     * Export precision per user untuk analisis Python
     */
    public function exportPrecisionPerUser()
    {
        $userPrecisions = CBFEvaluation::select(
                'user_id',
                DB::raw('COUNT(*) as total_recommendations'),
                DB::raw('SUM(CASE WHEN user_feedback = 1 THEN 1 ELSE 0 END) as relevant_recommendations'),
                DB::raw('SUM(CASE WHEN user_feedback = 0 THEN 1 ELSE 0 END) as not_relevant_recommendations')
            )
            ->whereNotNull('user_feedback')
            ->where('evaluation_source', 'user')
            ->whereHas('user', function ($query) {
                $query->where('is_cpns', true);
            })
            ->groupBy('user_id')
            ->with('user')
            ->get();
        
        $csv = "user_id,user_name,total_recommendations,relevant_recommendations,not_relevant_recommendations,precision,precision_pct\n";
        
        foreach ($userPrecisions as $row) {
            $precision = $row->total_recommendations > 0 
                ? $row->relevant_recommendations / $row->total_recommendations 
                : 0;
            
            $csv .= sprintf(
                "%d,\"%s\",%d,%d,%d,%.4f,%.2f\n",
                $row->user_id,
                str_replace('"', '""', $row->user->name ?? 'Unknown'),
                $row->total_recommendations,
                $row->relevant_recommendations,
                $row->not_relevant_recommendations,
                $precision,
                $precision * 100
            );
        }
        
        return response($csv)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="precision_per_user_' . date('Y-m-d') . '.csv"');
    }
    
    /**
     * Export evaluasi lengkap (detail per material)
     */
    public function exportCBFEvaluations()
    {
        $evaluations = CBFEvaluation::with(['user', 'setSoal', 'material'])
            ->whereNotNull('user_feedback')
            ->where('evaluation_source', 'user')
            ->whereHas('user', function ($query) {
                $query->where('is_cpns', true);
            })
            ->get();
        
        $csv = "user_id,user_name,set_soal_id,set_soal_title,material_id,material_title,material_kategori,similarity_score,user_feedback,is_relevant\n";
        
        foreach ($evaluations as $eval) {
            $csv .= sprintf(
                "%d,\"%s\",%d,\"%s\",%d,\"%s\",%s,%.4f,%d,%d\n",
                $eval->user_id,
                str_replace('"', '""', $eval->user ? $eval->user->name : 'Unknown'),
                $eval->set_soal_id,
                str_replace('"', '""', $eval->setSoal ? $eval->setSoal->title : 'Unknown'),
                $eval->material_id,
                str_replace('"', '""', $eval->material ? $eval->material->title : 'Unknown'),
                $eval->material ? $eval->material->kategori : 'Unknown',
                $eval->similarity_score ?? 0,
                $eval->user_feedback ? 1 : 0,
                $eval->user_feedback ? 1 : 0
            );
        }
        
        return response($csv)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="cbf_evaluations_' . date('Y-m-d') . '.csv"');
    }


    public function exportPretestPosttest()
{
    $posttests = HasilTryout::where('test_type', 'posttest')
        ->whereNotNull('pretest_id')
        ->whereNotNull('gain_score')
        ->with(['pretest', 'user', 'setSoal'])
        ->get();
    
    $csv = "user_id,user_name,pretest_score,posttest_score,max_score,gain_score,normalized_gain,category\n";
    
    foreach ($posttests as $post) {
        $pretest = $post->pretest;
        
        // Total score
        $pretestScore = $pretest->twk_score + $pretest->tiu_score + $pretest->tkp_score;
        $posttestScore = $post->twk_score + $post->tiu_score + $post->tkp_score;
        
        // Max score
        $setSoal = $post->setSoal;
        $maxScore = $setSoal ? ($setSoal->soals()->count() * 5) : 500;
        
        // Category
        $nGain = $post->normalized_gain;
        if ($nGain < 0.3) {
            $category = 'Rendah';
        } elseif ($nGain <= 0.7) {
            $category = 'Sedang';
        } else {
            $category = 'Tinggi';
        }
        
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
    
    return response($csv)
        ->header('Content-Type', 'text/csv; charset=UTF-8')
        ->header('Content-Disposition', 'attachment; filename="pretest_posttest_data_' . date('Y-m-d') . '.csv"');
}
}