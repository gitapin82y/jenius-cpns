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
        $maxScore = $setSoal ? ($setSoal->soal()->count() * 5) : 500;
        
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

/**
 * Export data evaluasi otomatis CBF untuk analisis Python
 */
public function exportAutomaticCBFEvaluations()
{
    if (!Auth::user()->is_admin) {
        abort(403, 'Unauthorized');
    }

    $evaluations = \App\Models\AutomaticCBFEvaluation::with([
        'user', 
        'setSoal', 
        'soal', 
        'material'
    ])
    ->whereHas('user', function($query) {
        $query->where('is_cpns', true); // Hanya user CPNS
    })
    ->orderBy('created_at', 'asc')
    ->get();

    if ($evaluations->isEmpty()) {
        return response()->json([
            'error' => 'Belum ada data evaluasi otomatis'
        ], 404);
    }

    // Header CSV
    $csv = "user_id,user_name,set_soal_id,set_soal_title,soal_id,soal_kategori,soal_tipe,material_id,material_title,material_kategori,soal_keywords,material_keywords,intersection_keywords,intersection_count,similarity_score,is_relevant,classification\n";

    foreach ($evaluations as $eval) {
        $csv .= sprintf(
            "%d,%s,%d,%s,%d,%s,%s,%d,%s,%s,\"%s\",\"%s\",\"%s\",%d,%.4f,%d,%s\n",
            $eval->user_id,
            $this->escapeCsv($eval->user->name ?? 'Unknown'),
            $eval->set_soal_id,
            $this->escapeCsv($eval->setSoal->title ?? 'Unknown'),
            $eval->soal_id,
            $eval->soal->kategori ?? 'Unknown',
            $this->escapeCsv($eval->soal->tipe ?? 'Unknown'),
            $eval->material_id,
            $this->escapeCsv($eval->material->title ?? 'Unknown'),
            $eval->material->kategori ?? 'Unknown',
            implode(';', $eval->soal_keywords ?? []),
            implode(';', $eval->material_keywords ?? []),
            implode(';', $eval->intersection_keywords ?? []),
            $eval->intersection_count,
            $eval->similarity_score,
            $eval->is_relevant ? 1 : 0,
            $eval->classification
        );
    }

    $filename = 'automatic_cbf_evaluations_' . date('Y-m-d_His') . '.csv';

    return response($csv)
        ->header('Content-Type', 'text/csv; charset=utf-8')
        ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
        ->header('Pragma', 'no-cache')
        ->header('Expires', '0');
}

/**
 * Export precision per user (dari evaluasi otomatis)
 */
public function exportAutomaticPrecisionPerUser()
{
    if (!Auth::user()->is_admin) {
        abort(403, 'Unauthorized');
    }

    // Hitung precision per user
    $userPrecisions = \DB::table('automatic_cbf_evaluations')
        ->join('users', 'automatic_cbf_evaluations.user_id', '=', 'users.id')
        ->join('set_soals', 'automatic_cbf_evaluations.set_soal_id', '=', 'set_soals.id')
        ->select(
            'automatic_cbf_evaluations.user_id',
            'users.name as user_name',
            'users.email as user_email',
            'automatic_cbf_evaluations.set_soal_id',
            'set_soals.title as set_soal_title',
            \DB::raw('COUNT(*) as total_recommendations'),
            \DB::raw('SUM(CASE WHEN classification = "TP" THEN 1 ELSE 0 END) as tp'),
            \DB::raw('SUM(CASE WHEN classification = "FP" THEN 1 ELSE 0 END) as fp'),
            \DB::raw('ROUND((SUM(CASE WHEN classification = "TP" THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) as precision')
        )
        ->where('users.is_cpns', true)
        ->groupBy('automatic_cbf_evaluations.user_id', 'users.name', 'users.email', 'automatic_cbf_evaluations.set_soal_id', 'set_soals.title')
        ->orderBy('users.name')
        ->get();

    if ($userPrecisions->isEmpty()) {
        return response()->json([
            'error' => 'Belum ada data precision'
        ], 404);
    }

    // Header CSV
    $csv = "user_id,user_name,user_email,set_soal_id,set_soal_title,total_recommendations,tp,fp,precision\n";

    foreach ($userPrecisions as $row) {
        $csv .= sprintf(
            "%d,%s,%s,%d,%s,%d,%d,%d,%.2f\n",
            $row->user_id,
            $this->escapeCsv($row->user_name),
            $this->escapeCsv($row->user_email),
            $row->set_soal_id,
            $this->escapeCsv($row->set_soal_title),
            $row->total_recommendations,
            $row->tp,
            $row->fp,
            $row->precision
        );
    }

    $filename = 'automatic_precision_per_user_' . date('Y-m-d_His') . '.csv';

    return response($csv)
        ->header('Content-Type', 'text/csv; charset=utf-8')
        ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
        ->header('Pragma', 'no-cache')
        ->header('Expires', '0');
}
/**
 * ✅ Export HANYA evaluasi manual (user feedback)
 */
public function exportUserManualEvaluations()
{
    if (!Auth::user()->is_admin) {
        abort(403, 'Unauthorized');
    }

    // Ambil HANYA yang sudah dinilai user
    $evaluations = \App\Models\AutomaticCBFEvaluation::with([
        'user', 
        'setSoal', 
        'soal', 
        'material'
    ])
    ->whereNotNull('user_feedback')  // ✅ Filter: hanya yang ada feedback
    ->whereHas('user', function($query) {
        $query->where('is_cpns', true);
    })
    ->orderBy('user_evaluated_at', 'asc')
    ->get();

    if ($evaluations->isEmpty()) {
        return response()->json([
            'error' => 'Belum ada data evaluasi manual dari user'
        ], 404);
    }

    // Header CSV
    $csv = "user_id,user_name,set_soal_id,set_soal_title,soal_id,soal_kategori,soal_tipe,material_id,material_title,material_kategori,similarity_score,threshold,automatic_classification,user_feedback,final_classification,evaluated_at\n";

    foreach ($evaluations as $eval) {
        $csv .= sprintf(
            "%d,%s,%d,%s,%d,%s,%s,%d,%s,%s,%.4f,%.4f,%s,%s,%s,%s\n",
            $eval->user_id,
            $this->escapeCsv($eval->user->name ?? 'Unknown'),
            $eval->set_soal_id,
            $this->escapeCsv($eval->setSoal->title ?? 'Unknown'),
            $eval->soal_id,
            $eval->soal->kategori ?? 'Unknown',
            $this->escapeCsv($eval->soal->tipe ?? 'Unknown'),
            $eval->material_id,
            $this->escapeCsv($eval->material->title ?? 'Unknown'),
            $eval->material->kategori ?? 'Unknown',
            $eval->similarity_score,
            $eval->threshold ?? 0.6,
            $eval->classification,
            $eval->user_feedback ? 'RELEVAN' : 'TIDAK RELEVAN',
            $eval->final_classification,
            $eval->user_evaluated_at ? $eval->user_evaluated_at->format('Y-m-d H:i:s') : ''
        );
    }

    $filename = 'user_manual_evaluations_' . date('Y-m-d_His') . '.csv';

    return response($csv)
        ->header('Content-Type', 'text/csv; charset=utf-8')
        ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
        ->header('Pragma', 'no-cache')
        ->header('Expires', '0');
}

public function exportUserManualPrecision()
{
    if (!Auth::user()->is_admin) {
        abort(403, 'Unauthorized');
    }

    // Hitung precision HANYA dari yang dinilai manual
    $userPrecisions = \DB::table('automatic_cbf_evaluations')
        ->join('users', 'automatic_cbf_evaluations.user_id', '=', 'users.id')
        ->join('set_soals', 'automatic_cbf_evaluations.set_soal_id', '=', 'set_soals.id')
        ->select(
            'automatic_cbf_evaluations.user_id',
            'users.name as user_name',
            'users.email as user_email',
            'automatic_cbf_evaluations.set_soal_id',
            'set_soals.title as set_soal_title',
            \DB::raw('COUNT(*) as total_evaluated'),
            \DB::raw('SUM(CASE WHEN user_feedback = 1 THEN 1 ELSE 0 END) as tp'),
            \DB::raw('SUM(CASE WHEN user_feedback = 0 THEN 1 ELSE 0 END) as fp'),
            \DB::raw('ROUND((SUM(CASE WHEN user_feedback = 1 THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) as `user_precision`') // ✅ Tambah backticks
        )
        ->whereNotNull('automatic_cbf_evaluations.user_feedback')
        ->where('users.is_cpns', 1) // ✅ Ganti true jadi 1
        ->groupBy('automatic_cbf_evaluations.user_id', 'users.name', 'users.email', 'automatic_cbf_evaluations.set_soal_id', 'set_soals.title')
        ->orderBy('users.name')
        ->get();

    if ($userPrecisions->isEmpty()) {
        return response()->json([
            'error' => 'Belum ada data precision manual'
        ], 404);
    }

    // Header CSV
    $csv = "user_id,user_name,user_email,set_soal_id,set_soal_title,total_evaluated,tp,fp,precision\n";

    foreach ($userPrecisions as $row) {
        $csv .= sprintf(
            "%d,%s,%s,%d,%s,%d,%d,%d,%.2f\n",
            $row->user_id,
            $this->escapeCsv($row->user_name),
            $this->escapeCsv($row->user_email),
            $row->set_soal_id,
            $this->escapeCsv($row->set_soal_title),
            $row->total_evaluated,
            $row->tp,
            $row->fp,
            $row->user_precision // ✅ Ganti dari precision jadi user_precision
        );
    }

    $filename = 'user_manual_precision_' . date('Y-m-d_His') . '.csv';

    return response($csv)
        ->header('Content-Type', 'text/csv; charset=utf-8')
        ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
        ->header('Pragma', 'no-cache')
        ->header('Expires', '0');
}
/**
 * Helper untuk escape CSV
 */
private function escapeCsv($value)
{
    return str_replace('"', '""', $value);
}
}