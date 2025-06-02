<?php

namespace App\Services;

use App\Models\Material;
use App\Models\Soal;
use App\Models\JawabanUser;
use App\Models\SetSoal;
use App\Models\RecommendationLog;
use App\Models\CBFEvaluation;

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
                    $soal->tipe
                );
                
                // Simpan ke database untuk cache
                $soal->update(['kata_kunci' => json_encode($keywords)]);
            }
            
            $soalKeywords = array_merge($soalKeywords, $keywords);
        }
        
        return $soalKeywords;
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
                    $material->tipe, 
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
     * STEP 6: Method utama dengan penyesuaian untuk latihan vs tryout resmi
     */
    public function generateMaterialRecommendations(int $userId, int $setSoalId): array
    {
        // Deteksi jenis tryout dan kategori focus
        $tryoutInfo = $this->detectTryoutTypeAndCategories($setSoalId);
        
        // STEP 1: Kumpulkan jawaban yang salah
        $wrongAnswers = $this->collectWrongAnswers($userId, $setSoalId);
        
        if ($wrongAnswers->isEmpty()) {
            return [
                'recommendations' => [],
                'tryout_info' => $tryoutInfo,
                'debug_info' => [
                    'message' => 'Tidak ada jawaban salah atau TKP poin 1 ditemukan',
                    'steps' => []
                ]
            ];
        }

        // STEP 2a: Extract kata kunci dari soal yang dijawab salah
        $soalKeywords = $this->extractWrongAnswerKeywords($wrongAnswers);
        
        // STEP 2b: Extract kata kunci dari materi (FILTER BERDASARKAN JENIS TRYOUT)
        $allowedCategories = null;
        
        // Jika ini latihan, fokus hanya pada kategori yang relevan
        if (!$tryoutInfo['is_tryout_resmi']) {
            $allowedCategories = $tryoutInfo['kategori_focus'];
        }
        // Jika tryout resmi, ambil semua kategori (null = semua)
        
        $allMaterialKeywords = $this->extractMaterialKeywordsByCategory($allowedCategories);
        
        // STEP 3: Gabungkan dan hapus duplikat kata kunci
        $uniqueKeywords = $this->combineAndRemoveDuplicateKeywords($soalKeywords, $allMaterialKeywords);
        
        // STEP 4: Konversi kata kunci soal ke vektor biner
        $soalVector = $this->convertToBobtVector($soalKeywords, $uniqueKeywords);
        
        // STEP 5 & 6: Loop setiap materi, hitung similarity, dan urutkan
        $similarities = [];
        
        // Query materi dengan filter kategori yang sama
        $materialsQuery = Material::where('status', 'Publish');
        if ($allowedCategories && !empty($allowedCategories)) {
            $materialsQuery->whereIn('kategori', $allowedCategories);
        }
        $materials = $materialsQuery->get();
        
        foreach ($materials as $material) {
            // Ambil kata kunci materi
            $materialKeywords = $material->kata_kunci 
                ? json_decode($material->kata_kunci, true) 
                : [];
            
            // Konversi kata kunci materi ke vektor biner
            $materialVector = $this->convertToBobtVector($materialKeywords, $uniqueKeywords);
            
            // Hitung cosine similarity
            $similarity = $this->calculateCosineSimilarity($soalVector, $materialVector);
            
            // Simpan hasil jika ada similarity
            if ($similarity > 0) {
                $similarities[] = [
                    'material' => $material,
                    'similarity' => $similarity,
                    'keywords' => $materialKeywords,
                    'vector' => $materialVector // Untuk debugging
                ];
            }
        }
        
        // Urutkan berdasarkan similarity tertinggi
        usort($similarities, function($a, $b) {
            return $b['similarity'] <=> $a['similarity'];
        });
        
        // Group berdasarkan kategori dan ambil top 5 per kategori
        $recommendations = [];
        
        // Inisialisasi berdasarkan jenis tryout
        if ($tryoutInfo['is_tryout_resmi']) {
            // Tryout resmi: tampilkan semua kategori
            $recommendations = [
                'TWK' => [],
                'TIU' => [],
                'TKP' => []
            ];
        } else {
            // Latihan: hanya kategori yang relevan
            foreach ($tryoutInfo['kategori_focus'] as $kategori) {
                $recommendations[$kategori] = [];
            }
        }
        
        foreach ($similarities as $item) {
            $kategori = $item['material']->kategori;
            if (isset($recommendations[$kategori]) && count($recommendations[$kategori]) < 5) {
                $recommendations[$kategori][] = $item;
            }
        }
        
        // Hitung statistik untuk debugging
        $wrongAnswersRegular = $wrongAnswers->where('status', 'salah');
        $tkpPoin1 = $wrongAnswers->filter(function($jawaban) {
            return $jawaban->soal->kategori === 'TKP' && $jawaban->status === 'benar';
        });
        
        // Debugging info
        $debugInfo = [
            'total_wrong_answers' => $wrongAnswers->count(),
            'regular_wrong_answers' => $wrongAnswersRegular->count(),
            'tkp_poin_1_count' => $tkpPoin1->count(),
            'soal_keywords' => $soalKeywords,
            'total_material_keywords' => count($allMaterialKeywords),
            'unique_keywords_count' => count($uniqueKeywords),
            'unique_keywords' => $uniqueKeywords,
            'soal_vector' => $soalVector,
            'total_similarities_calculated' => count($similarities),
            'filtered_categories' => $allowedCategories,
            'steps' => [
                'step_0' => 'Deteksi jenis: ' . ($tryoutInfo['is_tryout_resmi'] ? 'Tryout Resmi' : 'Latihan ' . implode(', ', $tryoutInfo['kategori_focus'])),
                'step_1' => 'Mengumpulkan ' . $wrongAnswers->count() . ' soal bermasalah (' . $wrongAnswersRegular->count() . ' salah + ' . $tkpPoin1->count() . ' TKP poin 1)',
                'step_2a' => 'Extract ' . count($soalKeywords) . ' kata kunci dari soal bermasalah',
                'step_2b' => 'Extract ' . count($allMaterialKeywords) . ' kata kunci dari materi' . ($allowedCategories ? ' (filter: ' . implode(', ', $allowedCategories) . ')' : ' (semua kategori)'),
                'step_3' => 'Gabung dan hapus duplikat, hasil: ' . count($uniqueKeywords) . ' kata kunci unik',
                'step_4' => 'Konversi ke vektor biner (panjang: ' . count($soalVector) . ')',
                'step_5' => 'Hitung cosine similarity untuk ' . $materials->count() . ' materi',
                'step_6' => 'Urutkan dan kelompokkan hasil rekomendasi berdasarkan kategori yang relevan'
            ]
        ];

          $result = [
        'recommendations' => $recommendations, // Ini akan menghasilkan struktur yang diharapkan blade
        'tryout_info' => $tryoutInfo,
        'debug_info' => $debugInfo
    ];

    // Log recommendations dengan struktur yang benar
    $this->logRecommendations($userId, $setSoalId, $result);
    
    return $result;
    }

private function logRecommendations($userId, $setSoalId, $fullResult)
{
    RecommendationLog::create([
        'user_id' => $userId,
        'set_soal_id' => $setSoalId,
        'recommendations' => $fullResult['recommendations'],
        'debug_info' => $fullResult['debug_info']
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
            'tryout_info' => $allRecommendations['tryout_info'],
            'debug_info' => $allRecommendations['debug_info']
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