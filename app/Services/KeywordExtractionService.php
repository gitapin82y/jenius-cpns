<?php

namespace App\Services;

class KeywordExtractionService
{
    /**
     * Core Keywords = Sub Kategori itu sendiri (sederhana dan tepat)
     */
    private $coreKeywords = [
        // TWK - Sub Kategori sebagai Keywords
        'Nasionalisme' => ['nasionalisme'],
        'Integritas' => ['integritas'],
        'Bela Negara' => ['bela negara'],
        'Pilar Negara' => ['pilar negara'],
        'Bahasa Indonesia' => ['bahasa indonesia'],
        
        // TIU - Sub Kategori sebagai Keywords
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
        
        // TKP - Sub Kategori sebagai Keywords
        'Pelayanan Publik' => ['pelayanan publik'],
        'Jejaring Kerja' => ['jejaring kerja'],
        'Sosial Budaya' => ['sosial budaya'],
        'Teknologi Informasi dan Komunikasi (TIK)' => ['teknologi informasi komunikasi'],
        'Profesionalisme' => ['profesionalisme'],
        'Anti Radikalisme' => ['anti radikalisme'],
    ];

    /**
     * Stop words yang sangat lengkap - buang semua noise
     */
    private $stopWords = [
        // Kata tanya - BUANG SEMUA
        'apa', 'siapa', 'kapan', 'dimana', 'di mana', 'mengapa', 'bagaimana', 'mana', 'berapa',
        'kenapa', 'gimana', 'seberapa', 'bilamana',
        
        // Kata hubung - BUANG SEMUA
        'dan', 'atau', 'serta', 'dengan', 'tanpa', 'untuk', 'dari', 'ke', 'di', 'pada', 'dalam',
        'oleh', 'bagi', 'tentang', 'mengenai', 'terhadap', 'atas', 'bawah', 'antara', 'hingga',
        
        // Kata kerja bantu - BUANG SEMUA
        'adalah', 'ialah', 'merupakan', 'yaitu', 'yakni', 'akan', 'telah', 'sudah', 'sedang',
        'pernah', 'belum', 'tidak', 'bukan', 'jangan', 'dapat', 'bisa', 'mampu', 'harus',
        
        // Kata ganti - BUANG SEMUA
        'ini', 'itu', 'tersebut', 'berikut', 'dia', 'ia', 'mereka', 'kita', 'kami', 'saya',
        'anda', 'beliau', 'yang', 'masing', 'setiap', 'semua', 'seluruh', 'sebagian',
        
        // Kata keterangan - BUANG SEMUA
        'sangat', 'amat', 'sekali', 'terlalu', 'cukup', 'agak', 'rada', 'lumayan',
        'hampir', 'nyaris', 'kurang', 'lebih', 'paling', 'ter', 'se', 'begitu',
        
        // Kata sambung - BUANG SEMUA
        'tetapi', 'namun', 'akan', 'meski', 'walaupun', 'meskipun', 'jika', 'kalau',
        'bila', 'apabila', 'ketika', 'saat', 'sewaktu', 'karena', 'sebab', 'sehingga',
        'supaya', 'agar', 'demi',
        
        // Kata bantu tempat - BUANG SEMUA
        'ada', 'adanya', 'berada', 'terdapat', 'terletak', 'memiliki', 'mempunyai',
        'berupa', 'bersifat', 'berwujud', 'hal', 'cara', 'jenis', 'macam', 'bentuk',
        
        // Kata noise soal/materi - BUANG SEMUA
        'soal', 'materi', 'belajar', 'contoh', 'latihan', 'ujian', 'test', 'quiz',
        'pertanyaan', 'jawaban', 'pembahasan', 'penjelasan', 'uraian',

        //
        'strong','pergerakan','termasuk','dibawah','sebagai', 
        
        // Stop words English - BUANG SEMUA
        'the', 'and', 'or', 'in', 'on', 'at', 'to', 'for', 'of', 'with', 'by', 'from',
        'is', 'are', 'was', 'were', 'be', 'been', 'have', 'has', 'had', 'do', 'does',
        'did', 'will', 'would', 'could', 'should', 'may', 'might', 'can', 'must',
        'this', 'that', 'these', 'those', 'a', 'an', 'but', 'if', 'when', 'where',
        'why', 'how', 'what', 'who', 'which', 'as', 'so', 'than', 'too', 'very'
    ];

    /**
     * Kata penting yang harus dipertahankan
     */
    private $importantWords = [
        'pancasila', 'indonesia', 'negara', 'bangsa', 'konstitusi', 'UUD',
        'matematika', 'logika', 'analisis', 'pola', 'deret', 'bilangan',
        'pelayanan', 'kerja', 'teknologi', 'informasi', 'digital',
        'ASN', 'PNS', 'CPNS', 'TWK', 'TIU', 'TKP'
    ];

    /**
     * Extract keywords dengan prioritas: Sub Kategori + Title saja
     */
    public function extractKeywords(string $title, string $tipe, string $content = null): array
    {
        $keywords = [];
        
        // PRIORITAS 1: Sub kategori sebagai keyword utama (selalu ada)
        if (isset($this->coreKeywords[$tipe])) {
            $keywords = $this->coreKeywords[$tipe];
        }
        
        // PRIORITAS 2: Extract dari title (maksimal 3 kata penting)
        $titleKeywords = $this->extractFromTitle($title);
        $keywords = array_merge($keywords, $titleKeywords);
        
        // PRIORITAS 3: Extract dari content (hanya jika perlu, maksimal 2 kata)
        if ($content && strlen($content) > 100) {
            $contentKeywords = $this->extractFromContent($content);
            $keywords = array_merge($keywords, $contentKeywords);
        }
        
        // Bersihkan dan filter
        $keywords = $this->cleanKeywords($keywords);
        
        // LIMIT: Maksimal 5-6 kata kunci total
        return array_slice($keywords, 0, 6);
    }

    /**
     * Extract keywords dari title dengan filter ketat
     */
    private function extractFromTitle(string $title): array
    {
        // Bersihkan title
        $title = strtolower($title);
        $title = preg_replace('/[^\w\s]/', ' ', $title);
        $title = preg_replace('/\s+/', ' ', $title);
        
        // Split dan filter
        $words = array_filter(
            preg_split('/\s+/', trim($title)),
            [$this, 'isImportantWord']
        );
        
        // Prioritaskan kata penting
        $prioritized = [];
        $normal = [];
        
        foreach ($words as $word) {
            if (in_array(strtolower($word), $this->importantWords)) {
                $prioritized[] = $word;
            } else {
                $normal[] = $word;
            }
        }
        
        // Gabung dengan prioritas, maksimal 3 kata
        $result = array_merge($prioritized, $normal);
        return array_slice($result, 0, 3);
    }

    /**
     * Extract keywords dari content (sangat terbatas)
     */
    private function extractFromContent(string $content): array
    {
        // Bersihkan content
        $content = strtolower($content);
        $content = preg_replace('/[^\w\s]/', ' ', $content);
        
        // Ambil kata-kata penting saja
        $words = array_filter(
            preg_split('/\s+/', $content),
            [$this, 'isImportantWord']
        );
        
        // Hitung frekuensi dan ambil 2 teratas
        $wordCount = array_count_values($words);
        arsort($wordCount);
        
        return array_slice(array_keys($wordCount), 0, 2);
    }

    /**
     * Cek apakah kata penting
     */
    private function isImportantWord(string $word): bool
    {
        $word = trim($word);
        
        // Minimal 3 karakter
        if (strlen($word) < 3) {
            return false;
        }
        
        // Skip stop words
        if (in_array(strtolower($word), $this->stopWords)) {
            return false;
        }
        
        // Skip pure numbers
        if (is_numeric($word)) {
            return false;
        }
        
        return true;
    }

    /**
     * Bersihkan keywords final
     */
    private function cleanKeywords(array $keywords): array
    {
        // Normalisasi
        $keywords = array_map('strtolower', $keywords);
        $keywords = array_map('trim', $keywords);
        
        // Hapus duplikat
        $keywords = array_unique($keywords);
        
        // Filter final
        $keywords = array_filter($keywords, function($keyword) {
            return strlen($keyword) > 2 && !in_array($keyword, $this->stopWords);
        });
        
        return array_values($keywords);
    }

    /**
     * Generate kata kunci suggestion (sub kategori saja)
     */
    public function generateKeywordSuggestions(string $tipe): array
    {
        return $this->coreKeywords[$tipe] ?? [];
    }
}