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
            $this->logRecommendations($userId, $setSoalId, []);
            return [
                'recommendations' => [],
                'tryout_info' => $tryoutInfo
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
      

      $this->logRecommendations($userId, $setSoalId, $recommendations);

      return [
            'recommendations' => $recommendations,
            'tryout_info' => $tryoutInfo
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