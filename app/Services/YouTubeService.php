<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class YouTubeService
{
    private $apiKey;
    private $baseUrl = 'https://www.googleapis.com/youtube/v3';

    public function __construct()
    {
        $this->apiKey = env('YOUTUBE_API_KEY'); // Tambahkan ke .env
    }

    /**
     * Search YouTube videos berdasarkan query
     */
    public function searchVideos(string $query, int $maxResults = 5): array
    {
        // Cache hasil selama 1 jam
        $cacheKey = 'youtube_search_' . md5($query . $maxResults);
        
        return Cache::remember($cacheKey, 3600, function() use ($query, $maxResults) {
            try {
                if (!$this->apiKey) {
                    return $this->getFallbackVideos($query);
                }

                $response = Http::get($this->baseUrl . '/search', [
                    'key' => $this->apiKey,
                    'q' => $query . ' CPNS tutorial belajar',
                    'part' => 'snippet',
                    'type' => 'video',
                    'maxResults' => $maxResults,
                    'order' => 'relevance',
                    'regionCode' => 'ID',
                    'relevanceLanguage' => 'id'
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    return $this->formatVideoResults($data['items'] ?? []);
                }

                return $this->getFallbackVideos($query);

            } catch (\Exception $e) {
                \Log::error('YouTube API Error: ' . $e->getMessage());
                return $this->getFallbackVideos($query);
            }
        });
    }

    /**
     * Format hasil video dari API
     */
    private function formatVideoResults(array $items): array
    {
        $videos = [];
        
        foreach ($items as $item) {
            $videos[] = [
                'title' => $item['snippet']['title'],
                'description' => $item['snippet']['description'],
                'thumbnail' => $item['snippet']['thumbnails']['medium']['url'] ?? '',
                'url' => 'https://www.youtube.com/watch?v=' . $item['id']['videoId'],
                'channel' => $item['snippet']['channelTitle'],
                'published_at' => $item['snippet']['publishedAt']
            ];
        }
        
        return $videos;
    }

    /**
     * Fallback videos jika API tidak tersedia
     */
    private function getFallbackVideos(string $query): array
    {
        // Video fallback berdasarkan kategori
        $fallbackVideos = [
            'TWK' => [
                [
                    'title' => 'FOKUS TWK #2 – MATERI DAN SOAL NASIONALISME',
                    'description' => 'Pembahasan lengkap materi nasionalisme untuk CPNS',
                    'thumbnail' => '/assets/img/video-placeholder.jpg',
                    'url' => '#',
                    'channel' => 'CPNS Channel',
                    'published_at' => '2024-01-01'
                ],
                [
                    'title' => 'Mengenal Nasionalisme, Patriotisme, Cinta Tanah Air dan Bela Negara',
                    'description' => 'Materi TWK tentang nasionalisme dan bela negara',
                    'thumbnail' => '/assets/img/video-placeholder.jpg',
                    'url' => '#',
                    'channel' => 'Belajar CPNS',
                    'published_at' => '2024-01-01'
                ]
            ],
            'TIU' => [
                [
                    'title' => 'Pola Deret Angka Beserta Contohnya - TIU CPNS',
                    'description' => 'Tutorial lengkap deret angka untuk TIU CPNS',
                    'thumbnail' => '/assets/img/video-placeholder.jpg',
                    'url' => '#',
                    'channel' => 'Math CPNS',
                    'published_at' => '2024-01-01'
                ]
            ],
            'TKP' => [
                [
                    'title' => 'Strategi Mengerjakan Soal TKP CPNS',
                    'description' => 'Tips dan trik mengerjakan soal TKP',
                    'thumbnail' => '/assets/img/video-placeholder.jpg',
                    'url' => '#',
                    'channel' => 'TKP Master',
                    'published_at' => '2024-01-01'
                ]
            ]
        ];

        // Tentukan kategori berdasarkan query
        $kategori = 'TWK'; // default
        if (stripos($query, 'numerik') !== false || stripos($query, 'verbal') !== false || stripos($query, 'figural') !== false) {
            $kategori = 'TIU';
        } elseif (stripos($query, 'pelayanan') !== false || stripos($query, 'jejaring') !== false || stripos($query, 'sosial') !== false) {
            $kategori = 'TKP';
        }

        return $fallbackVideos[$kategori] ?? $fallbackVideos['TWK'];
    }

    /**
     * Search videos berdasarkan multiple keywords
     */
    public function searchVideosByKeywords(array $keywords, int $maxResults = 3): array
    {
        $query = implode(' ', array_slice($keywords, 0, 3)); // Ambil 3 kata kunci teratas
        return $this->searchVideos($query, $maxResults);
    }
}