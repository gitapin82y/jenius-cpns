<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\Soal;
use App\Services\KeywordExtractionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KeywordUpdateController extends Controller
{
    private $keywordService;

    public function __construct(KeywordExtractionService $keywordService)
    {
        $this->keywordService = $keywordService;
    }

    /**
     * Update semua keywords - materials dan soals
     */
    public function updateAllKeywords(Request $request)
    {
        // Hanya admin yang bisa akses
        if (!Auth::user() || !Auth::user()->is_admin) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        try {
            DB::beginTransaction();

            $results = [
                'materials' => $this->updateMaterialKeywords(),
                'soals' => $this->updateSoalKeywords(),
                'timestamp' => now()->format('Y-m-d H:i:s')
            ];

            DB::commit();

            // Log aktivitas
            Log::info('Keywords updated by admin', [
                'user_id' => Auth::id(),
                'materials_updated' => $results['materials']['updated'],
                'soals_updated' => $results['soals']['updated']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Keywords berhasil diupdate!',
                'data' => $results
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('Error updating keywords', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update keywords untuk materials saja
     */
    public function updateMaterialKeywords()
    {
        $materials = Material::all();
        $updated = 0;
        $failed = 0;
        $details = [];

        foreach ($materials as $material) {
            try {
                // Backup keywords lama
                $oldKeywords = $material->kata_kunci ? json_decode($material->kata_kunci, true) : [];
                
                // Generate keywords baru
                $newKeywords = $this->keywordService->extractKeywords(
                    $material->title,
                    $material->tipe,
                    $material->content
                );

                // Update ke database
                $material->update(['kata_kunci' => json_encode($newKeywords)]);

                $details[] = [
                    'id' => $material->id,
                    'title' => $material->title,
                    'tipe' => $material->tipe,
                    'old_keywords_count' => count($oldKeywords),
                    'new_keywords_count' => count($newKeywords),
                    'old_keywords' => $oldKeywords,
                    'new_keywords' => $newKeywords
                ];

                $updated++;

            } catch (\Exception $e) {
                $failed++;
                Log::warning('Failed to update material keywords', [
                    'material_id' => $material->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return [
            'total' => $materials->count(),
            'updated' => $updated,
            'failed' => $failed,
            'details' => $details
        ];
    }

    /**
     * Update keywords untuk soals saja
     */
    public function updateSoalKeywords()
    {
        $soals = Soal::all();
        $updated = 0;
        $failed = 0;
        $details = [];

        foreach ($soals as $soal) {
            try {
                // Backup keywords lama
                $oldKeywords = $soal->kata_kunci ? json_decode($soal->kata_kunci, true) : [];
                
                // Generate keywords baru
                $newKeywords = $this->keywordService->extractKeywords(
                    $soal->pertanyaan,
                    $soal->tipe
                );

                // Update ke database
                $soal->update(['kata_kunci' => json_encode($newKeywords)]);

                $details[] = [
                    'id' => $soal->id,
                    'pertanyaan' => substr($soal->pertanyaan, 0, 50) . '...',
                    'tipe' => $soal->tipe,
                    'kategori' => $soal->kategori,
                    'old_keywords_count' => count($oldKeywords),
                    'new_keywords_count' => count($newKeywords),
                    'old_keywords' => $oldKeywords,
                    'new_keywords' => $newKeywords
                ];

                $updated++;

            } catch (\Exception $e) {
                $failed++;
                Log::warning('Failed to update soal keywords', [
                    'soal_id' => $soal->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return [
            'total' => $soals->count(),
            'updated' => $updated,
            'failed' => $failed,
            'details' => $details
        ];
    }

    /**
     * Update keywords untuk material tertentu
     */
    public function updateSingleMaterial(Request $request, $id)
    {
        if (!Auth::user() || !Auth::user()->is_admin) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $material = Material::findOrFail($id);
            
            $oldKeywords = $material->kata_kunci ? json_decode($material->kata_kunci, true) : [];
            
            $newKeywords = $this->keywordService->extractKeywords(
                $material->title,
                $material->tipe,
                $material->content
            );

            $material->update(['kata_kunci' => json_encode($newKeywords)]);

            return response()->json([
                'success' => true,
                'message' => 'Keywords material berhasil diupdate!',
                'data' => [
                    'material_id' => $id,
                    'old_keywords' => $oldKeywords,
                    'new_keywords' => $newKeywords
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update keywords untuk soal tertentu
     */
    public function updateSingleSoal(Request $request, $id)
    {
        if (!Auth::user() || !Auth::user()->is_admin) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $soal = Soal::findOrFail($id);
            
            $oldKeywords = $soal->kata_kunci ? json_decode($soal->kata_kunci, true) : [];
            
            $newKeywords = $this->keywordService->extractKeywords(
                $soal->pertanyaan,
                $soal->tipe
            );

            $soal->update(['kata_kunci' => json_encode($newKeywords)]);

            return response()->json([
                'success' => true,
                'message' => 'Keywords soal berhasil diupdate!',
                'data' => [
                    'soal_id' => $id,
                    'old_keywords' => $oldKeywords,
                    'new_keywords' => $newKeywords
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tampilkan halaman update keywords
     */
    public function showUpdatePage()
    {
        if (!Auth::user() || !Auth::user()->is_admin) {
            return redirect('/')->with('error', 'Unauthorized access');
        }

        // Hitung statistik
        $stats = [
            'total_materials' => Material::count(),
            'materials_with_keywords' => Material::whereNotNull('kata_kunci')->count(),
            'materials_without_keywords' => Material::whereNull('kata_kunci')->count(),
            'total_soals' => Soal::count(),
            'soals_with_keywords' => Soal::whereNotNull('kata_kunci')->count(),
            'soals_without_keywords' => Soal::whereNull('kata_kunci')->count(),
        ];

        return view('admin.keyword-update', compact('stats'));
    }

    /**
     * Preview keywords yang akan diupdate
     */
    public function previewKeywords(Request $request)
    {
        if (!Auth::user() || !Auth::user()->is_admin) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $type = $request->input('type', 'all'); // all, materials, soals
        $limit = $request->input('limit', 5);

        $preview = [];

        if ($type === 'all' || $type === 'materials') {
            $materials = Material::limit($limit)->get();
            foreach ($materials as $material) {
                $oldKeywords = $material->kata_kunci ? json_decode($material->kata_kunci, true) : [];
                $newKeywords = $this->keywordService->extractKeywords(
                    $material->title,
                    $material->tipe,
                    $material->content
                );

                $preview['materials'][] = [
                    'id' => $material->id,
                    'title' => $material->title,
                    'tipe' => $material->tipe,
                    'old_keywords' => $oldKeywords,
                    'new_keywords' => $newKeywords,
                    'improvement' => [
                        'old_count' => count($oldKeywords),
                        'new_count' => count($newKeywords),
                        'reduced_by' => count($oldKeywords) - count($newKeywords)
                    ]
                ];
            }
        }

        if ($type === 'all' || $type === 'soals') {
            $soals = Soal::limit($limit)->get();
            foreach ($soals as $soal) {
                $oldKeywords = $soal->kata_kunci ? json_decode($soal->kata_kunci, true) : [];
                $newKeywords = $this->keywordService->extractKeywords(
                    $soal->pertanyaan,
                    $soal->tipe
                );

                $preview['soals'][] = [
                    'id' => $soal->id,
                    'pertanyaan' => substr($soal->pertanyaan, 0, 50) . '...',
                    'tipe' => $soal->tipe,
                    'old_keywords' => $oldKeywords,
                    'new_keywords' => $newKeywords,
                    'improvement' => [
                        'old_count' => count($oldKeywords),
                        'new_count' => count($newKeywords),
                        'reduced_by' => count($oldKeywords) - count($newKeywords)
                    ]
                ];
            }
        }

        return response()->json([
            'success' => true,
            'preview' => $preview
        ]);
    }
}