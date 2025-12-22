<?php

namespace App\Services;

class KeywordExtractionService
{
    /**
     * ✅ BARU: Kategori keywords (TWK/TIU/TKP)
     */
    private $categoryKeywords = [
        'TWK' => ['twk'],
        'TIU' => ['tiu'],
        'TKP' => ['tkp']
    ];

    /**
     * Core Keywords = Sub Kategori (existing - TIDAK BERUBAH)
     */
    private $coreKeywords = [
        // TWK
        'Nasionalisme' => ['nasionalisme'],
        'Integritas' => ['integritas'],
        'Bela Negara' => ['bela negara'],
        'Pilar Negara' => ['pilar negara'],
        'Bahasa Indonesia' => ['bahasa indonesia'],
        
        // TIU
        'Verbal (Analogi)' => ['verbal analogi'],
        'Verbal (Silogisme)' => ['verbal silogisme'],
        'Verbal (Analisis)' => ['verbal analisis'],
        'Numerik (Hitung Cepat)' => ['numerik hitung cepat'],
        'Numerik (Deret Angka)' => ['numerik deret angka'],
        'Numerik (Perbandingan Kuantitatif)' => ['numerik perbandingan kuantitatif'],
        'Numerik (Soal Cerita)' => ['numerik soal cerita'],
        'Figural (Analogi)' => ['figural analogi'],
        'Figural (Ketidaksamaan)' => ['figural ketidaksamaan'],
        'Figural (Serial)' => ['figural serial'],
        
        // TKP
        'Pelayanan Publik' => ['pelayanan publik'],
        'Jejaring Kerja' => ['jejaring kerja'],
        'Sosial Budaya' => ['sosial budaya'],
        'Teknologi Informasi dan Komunikasi (TIK)' => ['teknologi informasi komunikasi'],
        'Profesionalisme' => ['profesionalisme'],
        'Anti Radikalisme' => ['anti radikalisme'],
    ];

    /**
     * Stop words (existing - TIDAK BERUBAH)
     */
    private $stopWords = [
        'apa', 'siapa', 'kapan', 'dimana', 'di mana', 'mengapa', 'bagaimana', 'mana', 'berapa',
        'kenapa', 'gimana', 'seberapa', 'bilamana',
        'dan', 'atau', 'serta', 'dengan', 'tanpa', 'untuk', 'dari', 'ke', 'di', 'pada', 'dalam',
        'oleh', 'bagi', 'tentang', 'mengenai', 'terhadap', 'atas', 'bawah', 'antara', 'hingga',
        'adalah', 'ialah', 'merupakan', 'yaitu', 'yakni', 'akan', 'telah', 'sudah', 'sedang',
        'pernah', 'belum', 'tidak', 'bukan', 'jangan', 'dapat', 'bisa', 'mampu', 'harus',
        'ini', 'itu', 'tersebut', 'berikut', 'dia', 'ia', 'mereka', 'kita', 'kami', 'saya',
        'anda', 'beliau', 'yang', 'masing', 'setiap', 'semua', 'seluruh', 'sebagian',
        'sangat', 'amat', 'sekali', 'terlalu', 'cukup', 'agak', 'rada', 'lumayan',
        'hampir', 'nyaris', 'kurang', 'lebih', 'paling', 'ter', 'se', 'begitu',
        'tetapi', 'namun', 'akan', 'meski', 'walaupun', 'meskipun', 'jika', 'kalau',
        'bila', 'apabila', 'ketika', 'saat', 'sewaktu', 'karena', 'sebab', 'sehingga',
        'supaya', 'agar', 'demi',
        'ada', 'adanya', 'berada', 'terdapat', 'terletak', 'memiliki', 'mempunyai',
        'berupa', 'bersifat', 'berwujud', 'hal', 'cara', 'jenis', 'macam', 'bentuk',
        'soal', 'materi', 'belajar', 'contoh', 'latihan', 'ujian', 'test', 'quiz',
        'pertanyaan', 'jawaban', 'pembahasan', 'penjelasan', 'uraian',
        'strong','pergerakan','termasuk','dibawah','sebagai', 'semangat', 'sejak','ikan',
        'the', 'and', 'or', 'in', 'on', 'at', 'to', 'for', 'of', 'with', 'by', 'from',
        'is', 'are', 'was', 'were', 'be', 'been', 'have', 'has', 'had', 'do', 'does',
        'did', 'will', 'would', 'could', 'should', 'may', 'might', 'can', 'must',
        'this', 'that', 'these', 'those', 'a', 'an', 'but', 'if', 'when', 'where',
        'why', 'how', 'what', 'who', 'which', 'as', 'so', 'than', 'too', 'very'
    ];

    /**
     * Kata penting (existing - TIDAK BERUBAH)
     */
    private $importantWords = [
        'pancasila', 'indonesia', 'negara', 'bangsa', 'konstitusi', 'UUD',
        'matematika', 'logika', 'analisis', 'pola', 'deret', 'bilangan',
        'pelayanan', 'kerja', 'teknologi', 'informasi', 'digital',
        'ASN', 'PNS', 'CPNS', 'TWK', 'TIU', 'TKP'
    ];

    /**
     * ✅ UPDATE: Extract keywords dengan kategori
     * 
     * @param string $title - Judul/pertanyaan
     * @param string $tipe - Sub-kategori (Nasionalisme, Verbal Analogi, dll)
     * @param string|null $kategori - Kategori utama (TWK/TIU/TKP) ← BARU
     */
    public function extractKeywords(string $title, string $tipe, ?string $kategori = null): array
    {
        $keywords = [];
        
        // ✅ PRIORITAS 0: Kategori (TWK/TIU/TKP) - BARU
        if ($kategori && isset($this->categoryKeywords[$kategori])) {
            $keywords = array_merge($keywords, $this->categoryKeywords[$kategori]);
        }
        
        // PRIORITAS 1: Sub kategori (existing)
        if (isset($this->coreKeywords[$tipe])) {
            $keywords = array_merge($keywords, $this->coreKeywords[$tipe]);
        }
        
        // PRIORITAS 2: Extract dari title (existing)
        $titleKeywords = $this->extractFromTitle($title);
        $keywords = array_merge($keywords, $titleKeywords);
        
        // Bersihkan dan filter (existing)
        $keywords = $this->cleanKeywords($keywords);
        
        // ✅ UPDATE: Limit maksimal 7-8 kata (naik dari 5-6)
        return array_slice($keywords, 0, 9);
    }

    /**
     * Extract dari title (existing - TIDAK BERUBAH)
     */
    private function extractFromTitle(string $title): array
    {
        $title = strtolower($title);
        $title = preg_replace('/[^\w\s]/', ' ', $title);
        $title = preg_replace('/\s+/', ' ', $title);
        
        $words = array_filter(
            preg_split('/\s+/', trim($title)),
            [$this, 'isImportantWord']
        );
        
        $prioritized = [];
        $normal = [];
        
        foreach ($words as $word) {
            if (in_array(strtolower($word), $this->importantWords)) {
                $prioritized[] = $word;
            } else {
                $normal[] = $word;
            }
        }
        
        $result = array_merge($prioritized, $normal);
        return array_slice($result, 0, 5);
    }

    /**
     * Cek kata penting (existing - TIDAK BERUBAH)
     */
    private function isImportantWord(string $word): bool
    {
        $word = trim($word);
        
        if (strlen($word) < 3) {
            return false;
        }
        
        if (in_array(strtolower($word), $this->stopWords)) {
            return false;
        }
        
        if (is_numeric($word)) {
            return false;
        }
        
        return true;
    }

    /**
     * Clean keywords (existing - TIDAK BERUBAH)
     */
    private function cleanKeywords(array $keywords): array
    {
        $keywords = array_map('strtolower', $keywords);
        $keywords = array_map('trim', $keywords);
        $keywords = array_unique($keywords);
        
        $keywords = array_filter($keywords, function($keyword) {
            return strlen($keyword) > 2 && !in_array($keyword, $this->stopWords);
        });
        
        return array_values($keywords);
    }

    /**
     * Generate suggestions (existing - TIDAK BERUBAH)
     */
    public function generateKeywordSuggestions(string $tipe): array
    {
        return $this->coreKeywords[$tipe] ?? [];
    }
}