<?php

namespace App\Services;

use App\Models\Material;
use App\Models\Soal;
use App\Models\JawabanUser;
use App\Models\SetSoal;
use App\Models\RecommendationLog;
use App\Models\CBFEvaluation;
use App\Models\Recommendation;

use Illuminate\Support\Collection;

class ContentBasedFilteringService
{
    private $keywordExtractionService;

    public function __construct(KeywordExtractionService $keywordExtractionService)
    {
        $this->keywordExtractionService = $keywordExtractionService;
    }

    /**
     * STEP 1: Pengumpulan Data Hasil Tryout
     * Mengambil soal yang dijawab salah + soal TKP dengan poin 1 (dianggap salah)
     */
    public function collectWrongAnswers(int $userId, int $setSoalId): Collection
    {
        return JawabanUser::where('user_id', $userId)
            ->where('set_soal_id', $setSoalId)
            ->where(function($query) {
                // Soal yang dijawab salah (TWK, TIU)
                $query->where('status', 'salah')
                    // ATAU soal TKP dengan poin 1 (dianggap salah)
                    ->orWhere(function($subQuery) {
                        $subQuery->whereHas('soal', function($soalQuery) {
                            $soalQuery->where('kategori', 'TKP');
                        })->where(function($scoreQuery) {
                            // Cek apakah user mendapat poin 1 di TKP
                            $scoreQuery->whereRaw('
                                CASE 
                                    WHEN jawaban_user = "A" THEN (SELECT score_a FROM soals WHERE soals.id = soal_id) 
                                    WHEN jawaban_user = "B" THEN (SELECT score_b FROM soals WHERE soals.id = soal_id)
                                    WHEN jawaban_user = "C" THEN (SELECT score_c FROM soals WHERE soals.id = soal_id)
                                    WHEN jawaban_user = "D" THEN (SELECT score_d FROM soals WHERE soals.id = soal_id)
                                    WHEN jawaban_user = "E" THEN (SELECT score_e FROM soals WHERE soals.id = soal_id)
                                    ELSE 0 
                                END = 1
                            ');
                        });
                    });
            })
            ->with('soal')
            ->get();
    }

    /**
     * STEP 2: Mengambil Kata Kunci dari Soal yang Dijawab Salah (termasuk TKP poin 1)
     */
    public function extractWrongAnswerKeywords(Collection $wrongAnswers): array
    {
        $soalKeywords = [];
        
        foreach ($wrongAnswers as $jawabanUser) {
            $soal = $jawabanUser->soal;
            
            // Ambil kata kunci dari database atau generate jika belum ada
            if ($soal->kata_kunci) {
                $keywords = json_decode($soal->kata_kunci, true);
            } else {
                // Generate kata kunci jika belum ada
                $keywords = $this->keywordExtractionService->extractKeywords(
                    $soal->pertanyaan, 
                    $soal->tipe,
                     $soal->kategori 
                );
                
                // Simpan ke database untuk cache
                $soal->update(['kata_kunci' => json_encode($keywords)]);
            }
            
            $soalKeywords = array_merge($soalKeywords, $keywords);
        }
        
        return $soalKeywords;
    }

    /**
 * ✅ BARU: Extract kata kunci dari 1 soal saja (tidak digabung)
 */
private function extractSingleSoalKeywords(Soal $soal): array
{
    if ($soal->kata_kunci) {
        return json_decode($soal->kata_kunci, true);
    }
    
    // Generate jika belum ada
    $keywords = $this->keywordExtractionService->extractKeywords(
        $soal->pertanyaan, 
        $soal->tipe,
         $soal->kategori 
    );
    
    // Simpan untuk cache
    $soal->update(['kata_kunci' => json_encode($keywords)]);
    
    return $keywords;
}

/**
 * ✅ BARU: Kumpulkan SEMUA kata kunci unik (untuk basis vektor)
 */
private function collectAllUniqueKeywords(Collection $wrongAnswers, Collection $materials): array
{
    $allKeywords = [];
    
    // Dari soal yang salah
    foreach ($wrongAnswers as $jawabanUser) {
        $soal = $jawabanUser->soal;
        $keywords = $this->extractSingleSoalKeywords($soal);
        $allKeywords = array_merge($allKeywords, $keywords);
    }
    
    // Dari materi
    foreach ($materials as $material) {
        $keywords = $material->kata_kunci 
            ? json_decode($material->kata_kunci, true) 
            : [];
        $allKeywords = array_merge($allKeywords, $keywords);
    }
    
    // Hapus duplikat dan normalisasi
    $unique = array_unique(array_map('strtolower', array_map('trim', $allKeywords)));
    
    // Filter kata terlalu pendek
    $unique = array_filter($unique, function($keyword) {
        return strlen(trim($keyword)) > 2;
    });
    
    return array_values($unique);
}

    /**
     * STEP 2: Mengambil Kata Kunci dari Materi Pembelajaran (FILTER BY KATEGORI)
     */
    public function extractMaterialKeywordsByCategory(array $allowedCategories = null): array
    {
        $query = Material::where('status', 'Publish');
        
        // Filter berdasarkan kategori jika ditentukan
        if ($allowedCategories && !empty($allowedCategories)) {
            $query->whereIn('kategori', $allowedCategories);
        }
        
        $materials = $query->get();
        $allMaterialKeywords = [];
        
        foreach ($materials as $material) {
            if ($material->kata_kunci) {
                $keywords = json_decode($material->kata_kunci, true);
            } else {
                // Generate kata kunci jika belum ada
                $keywords = $this->keywordExtractionService->extractKeywords(
                    $material->title, 
                    $material->tipe
                );
                
                // Simpan ke database untuk cache
                $material->update(['kata_kunci' => json_encode($keywords)]);
            }
            
            $allMaterialKeywords = array_merge($allMaterialKeywords, $keywords);
        }
        
        return $allMaterialKeywords;
    }

    /**
     * STEP 3: Menggabungkan Kata Kunci Soal dan Materi + Menghapus Duplikat
     */
    public function combineAndRemoveDuplicateKeywords(array $soalKeywords, array $materialKeywords): array
    {
        // Gabungkan semua kata kunci
        $combinedKeywords = array_merge($soalKeywords, $materialKeywords);
        
        // Hapus duplikat dan normalisasi
        $uniqueKeywords = array_unique(array_map('strtolower', $combinedKeywords));
        
        // Filter kata kunci yang terlalu pendek
        $uniqueKeywords = array_filter($uniqueKeywords, function($keyword) {
            return strlen(trim($keyword)) > 2;
        });
        
        return array_values($uniqueKeywords);
    }

    /**
     * STEP 4: Konversi ke Vektor Biner
     */
    public function convertToBobtVector(array $itemKeywords, array $uniqueKeywords): array
    {
        $vector = [];
        
        // Normalisasi kata kunci item
        $normalizedItemKeywords = array_map('strtolower', $itemKeywords);
        
        // Loop melalui setiap kata kunci unik
        foreach ($uniqueKeywords as $uniqueKeyword) {
            // 1 jika kata kunci ada, 0 jika tidak ada
            $vector[] = in_array(strtolower($uniqueKeyword), $normalizedItemKeywords) ? 1 : 0;
        }
        
        return $vector;
    }

    /**
     * STEP 5: Perhitungan Cosine Similarity
     */
    public function calculateCosineSimilarity(array $vector1, array $vector2): float
    {
        // Pastikan kedua vektor memiliki panjang yang sama
        if (count($vector1) !== count($vector2)) {
            return 0.0;
        }

        $dotProduct = 0;
        $magnitude1 = 0;
        $magnitude2 = 0;

        // Hitung dot product dan magnitude
        for ($i = 0; $i < count($vector1); $i++) {
            $dotProduct += $vector1[$i] * $vector2[$i];
            $magnitude1 += $vector1[$i] * $vector1[$i];
            $magnitude2 += $vector2[$i] * $vector2[$i];
        }

        // Hitung magnitude (akar dari sum of squares)
        $magnitude1 = sqrt($magnitude1);
        $magnitude2 = sqrt($magnitude2);

        // Hindari pembagian dengan nol
        if ($magnitude1 == 0 || $magnitude2 == 0) {
            return 0.0;
        }

        // Cosine similarity = dot product / (magnitude1 * magnitude2)
        return $dotProduct / ($magnitude1 * $magnitude2);
    }

    /**
     * Deteksi jenis tryout dan kategori soal
     */
    private function detectTryoutTypeAndCategories(int $setSoalId): array
    {
        $setSoal = SetSoal::findOrFail($setSoalId);
        
        // Cek apakah ini tryout resmi atau latihan
        $isTryoutResmi = ($setSoal->kategori === 'Tryout');
        
        // Ambil kategori soal yang ada dalam set soal ini
        $kategoriFocus = Soal::where('set_soal_id', $setSoalId)
            ->select('kategori')
            ->distinct()
            ->pluck('kategori')
            ->toArray();
        
        return [
            'is_tryout_resmi' => $isTryoutResmi,
            'kategori_focus' => $kategoriFocus,
            'set_soal_kategori' => $setSoal->kategori
        ];
    }

/**
 * ✅ REVISI: Generate recommendations dengan ONE-TO-ONE mapping
 */
public function generateMaterialRecommendations(int $userId, int $setSoalId): array
{
    // Deteksi jenis tryout
    $tryoutInfo = $this->detectTryoutTypeAndCategories($setSoalId);
    
    // STEP 1: Kumpulkan jawaban yang salah
    $wrongAnswers = $this->collectWrongAnswers($userId, $setSoalId);
    
    if ($wrongAnswers->isEmpty()) {
        $this->logRecommendations($userId, $setSoalId, []);
        return [
            'recommendations' => [],
            'tryout_info' => $tryoutInfo,
            'total_recommendations' => 0,
            'total_wrong_answers' => 0
        ];
    }

    // STEP 2: Ambil semua materi
    $allowedCategories = null;
    if (!$tryoutInfo['is_tryout_resmi']) {
        $allowedCategories = $tryoutInfo['kategori_focus'];
    }
    
    $materialsQuery = Material::where('status', 'Publish');
    if ($allowedCategories && !empty($allowedCategories)) {
        $materialsQuery->whereIn('kategori', $allowedCategories);
    }
    $allMaterials = $materialsQuery->get();
    
    // STEP 3: Kumpulkan SEMUA kata kunci unik (basis vektor)
    $allUniqueKeywords = $this->collectAllUniqueKeywords($wrongAnswers, $allMaterials);
    
    // ✅ PERUBAHAN UTAMA: Loop SETIAP soal salah
    $recommendationsPerSoal = [];
    $recommendationsByCategory = [];
    
    // Inisialisasi array berdasarkan kategori
    if ($tryoutInfo['is_tryout_resmi']) {
        $recommendationsByCategory = ['TWK' => [], 'TIU' => [], 'TKP' => []];
    } else {
        foreach ($tryoutInfo['kategori_focus'] as $kategori) {
            $recommendationsByCategory[$kategori] = [];
        }
    }
    
    // ✅ LOOP UNTUK SETIAP SOAL SALAH
    foreach ($wrongAnswers as $jawabanUser) {
        $soal = $jawabanUser->soal;
        
        // ✅ Ekstrak kata kunci SOAL INI SAJA
        $soalKeywords = $this->extractSingleSoalKeywords($soal);
        
        // ✅ Buat vektor untuk SOAL INI
        $soalVector = $this->convertToBobtVector($soalKeywords, $allUniqueKeywords);
        
        // ✅ Cari materi terbaik untuk SOAL INI
        $bestMaterial = null;
        $bestScore = -1;
        
        foreach ($allMaterials as $material) {
            // Filter: hanya materi dengan kategori yang sama
            if ($material->kategori !== $soal->kategori) {
                continue;
            }
            
            // Ekstrak kata kunci materi
            $materialKeywords = $material->kata_kunci 
                ? json_decode($material->kata_kunci, true) 
                : [];
            
            // Buat vektor materi
            $materialVector = $this->convertToBobtVector($materialKeywords, $allUniqueKeywords);
            
            // Hitung similarity
            $similarity = $this->calculateCosineSimilarity($soalVector, $materialVector);
            
            // Simpan jika ini similarity tertinggi
            if ($similarity > $bestScore) {
                $bestScore = $similarity;
                $bestMaterial = $material;
            }
        }
        
        // ✅ Simpan rekomendasi untuk SOAL INI
        if ($bestMaterial && $bestScore > 0) {
            $recommendationData = [
                'soal' => $soal,
                'soal_id' => $soal->id,
                'soal_pertanyaan' => $soal->pertanyaan,
                'soal_kategori' => $soal->kategori,
                'material' => $bestMaterial,
                'material_id' => $bestMaterial->id,
                'material_title' => $bestMaterial->title,
                'similarity' => $bestScore,
                'keywords' => $soalKeywords
            ];
            
            $recommendationsPerSoal[] = $recommendationData;
            $recommendationsByCategory[$soal->kategori][] = $recommendationData;
            
            // ✅ Simpan ke database
            Recommendation::updateOrCreate(
                [
                    'user_id' => $userId,
                    'set_soal_id' => $setSoalId,
                    'soal_id' => $soal->id
                ],
                [
                    'material_id' => $bestMaterial->id,
                    'similarity_score' => $bestScore
                ]
            );
        }
    }
    
    // Log untuk backward compatibility
    $this->logRecommendations($userId, $setSoalId, $recommendationsByCategory);

    return [
        'recommendations' => $recommendationsByCategory,
        'recommendations_per_soal' => $recommendationsPerSoal,
        'tryout_info' => $tryoutInfo,
        'total_recommendations' => count($recommendationsPerSoal),
        'total_wrong_answers' => $wrongAnswers->count()
    ];
}

private function logRecommendations($userId, $setSoalId, array $recommendations)
{
    RecommendationLog::create([
        'user_id' => $userId,
        'set_soal_id' => $setSoalId,
        'recommendations' => $recommendations,
        'debug_info' => []
    ]);
    
}

    /**
     * Generate rekomendasi berdasarkan kategori specific
     */
    public function generateCategorySpecificRecommendations(int $userId, int $setSoalId, string $kategori): array
    {
        $allRecommendations = $this->generateMaterialRecommendations($userId, $setSoalId);
        
        return [
            'recommendations' => $allRecommendations['recommendations'][$kategori] ?? [],
            'tryout_info' => $allRecommendations['tryout_info']
        ];
    }

    /**
     * Legacy method untuk backward compatibility
     */
    public function extractAllMaterialKeywords(): array
    {
        return $this->extractMaterialKeywordsByCategory();
    }
}