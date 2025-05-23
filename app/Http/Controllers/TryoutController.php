<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SetSoal;
use App\Models\HasilTryout;
use App\Models\JawabanUser;
use App\Models\Soal;
use App\Models\SystemError;
use App\Models\Material;
use App\Models\UserMaterialProgress;
use App\Services\ContentBasedFilteringService;
use App\Services\YouTubeService;
use RealRashid\SweetAlert\Facades\Alert;

class TryoutController extends Controller
{
    private $cbfService;
    private $youtubeService;

    public function __construct(
        ContentBasedFilteringService $cbfService,
        YouTubeService $youtubeService
    ) {
        $this->cbfService = $cbfService;
        $this->youtubeService = $youtubeService;
    }

    public function index($set_soal_id)
    {
        try {
            $setSoal = SetSoal::findOrFail($set_soal_id);
            $user = Auth::user();
            
            // Periksa apakah pengguna berhak mengakses latihan ini
            if ($setSoal->kategori == 'Latihan') {
                // Periksa kategori soal dari set soal ini
                $firstSoal = Soal::where('set_soal_id', $set_soal_id)->first();
                $kategoriSoal = $firstSoal ? $firstSoal->kategori : null;
                
                // Periksa apakah semua materi kategori tersebut telah diselesaikan
                if ($kategoriSoal) {
                    $totalMaterials = Material::where('kategori', $kategoriSoal)
                        ->where('status', 'Publish')
                        ->count();
                    
                    $completedMaterials = UserMaterialProgress::where('user_id', $user->id)
                        ->whereHas('material', function($query) use ($kategoriSoal) {
                            $query->where('kategori', $kategoriSoal)
                                ->where('status', 'Publish');
                        })
                        ->where('is_completed', true)
                        ->count();
                    
                    if ($totalMaterials > 0 && $completedMaterials < $totalMaterials) {
                        toast()->warning('Anda harus menyelesaikan semua materi '.$kategoriSoal.' terlebih dahulu.');
                        return redirect()->route('public.materi.index');
                    }
                    
                    // Jika kategori sebelumnya, periksa apakah latihannya sudah diselesaikan
                    if ($kategoriSoal == 'TIU') {
                        // Periksa apakah latihan TWK sudah selesai
                        $latihan_twk = SetSoal::where('kategori', 'Latihan')
                            ->whereHas('soal', function($query) {
                                $query->where('kategori', 'TWK');
                            })
                            ->first();
                            
                        if ($latihan_twk) {
                            $twk_completed = HasilTryout::where('user_id', $user->id)
                                ->where('set_soal_id', $latihan_twk->id)
                                ->exists();
                                
                            if (!$twk_completed) {
                                toast()->warning('Anda harus menyelesaikan latihan TWK terlebih dahulu.');
                                return redirect()->route('public.materi.index');
                            }
                        }
                    } else if ($kategoriSoal == 'TKP') {
                        // Periksa apakah latihan TWK dan TIU sudah selesai
                        $latihan_tiu = SetSoal::where('kategori', 'Latihan')
                            ->whereHas('soal', function($query) {
                                $query->where('kategori', 'TIU');
                            })
                            ->first();
                            
                        if ($latihan_tiu) {
                            $tiu_completed = HasilTryout::where('user_id', $user->id)
                                ->where('set_soal_id', $latihan_tiu->id)
                                ->exists();
                                
                            if (!$tiu_completed) {
                                toast()->warning('Anda harus menyelesaikan latihan TIU terlebih dahulu.');
                                return redirect()->route('public.materi.index');
                            }
                        }
                    }
                }
            } else if ($setSoal->kategori == 'Tryout') {
                // Periksa apakah pengguna sudah menyelesaikan semua materi dan latihan
                if (!$user->is_akses) {
                    // Periksa apakah semua latihan sudah selesai
                    $latihan_sets = SetSoal::where('kategori', 'Latihan')
                        ->where('status', 'Publish')
                        ->pluck('id')
                        ->toArray();
                    
                    $completed_latihan = HasilTryout::where('user_id', $user->id)
                        ->whereIn('set_soal_id', $latihan_sets)
                        ->pluck('set_soal_id')
                        ->toArray();
                    
                    if (count($completed_latihan) < count($latihan_sets)) {
                          return redirect()->route('public.materi.index')->with([
                        'sweetAlert' => true,
                        'type' => 'warning',
                        'title' => 'Akses Ditolak!',
                        'text' => 'Anda harus menyelesaikan semua materi dan latihan terlebih dahulu.'
                    ]);
                    } else {
                        // Jika semua sudah selesai, aktifkan is_akses
                        $user->is_akses = true;
                        $user->save();
                    }
                }
            }
            
            // Ambil soal untuk ditampilkan
            $soals = Soal::where('set_soal_id', $set_soal_id)
                ->orderByRaw("FIELD(kategori, 'TWK', 'TIU', 'TKP')")
                ->get();
            
            return view('login.tryout.index', compact('soals', 'user', 'set_soal_id'));
        } catch (\Exception $e) {
            // Log error
            SystemErrorController::logError(
                Auth::id(), 
                $e->getCode() ?: '500', 
                'Server Error', 
                $e->getMessage()
            );
            
            // Notify user
            toast()->error('Terjadi kesalahan pada sistem.');
            return redirect()->back();
        }
    }

    public function submit(Request $request)
    {
        try {
            $jawabanUsers = $request->input('jawaban_users');
            $set_soal_id = $request->input('set_soal_id');
            $user = Auth::user();

            if ($user->paket_id == 1) {
                $user->is_akses = false;
                $user->save();
            }

            $soals = Soal::where('set_soal_id', $set_soal_id)->get();
        
            // Initialize category and type scores
            $scores = [
                'twk_score' => 0,
                'tiu_score' => 0,
                'tkp_score' => 0,
                'total_benar' => 0,
                'total_salah' => 0,
                'total_kosong' => 0,
                'nasionalisme' => 0,
                'integritas' => 0,
                'bela_negara' => 0,
                'pilar_negara' => 0,
                'bahasa_indonesia' => 0,
                'verbal_analogi' => 0,
                'verbal_silogisme' => 0,
                'verbal_analisis' => 0,
                'numerik_hitung_cepat' => 0,
                'numerik_deret_angka' => 0,
                'numerik_perbandingan_kuantitatif' => 0,
                'numerik_soal_cerita' => 0,
                'figural_analogi' => 0,
                'figural_ketidaksamaan' => 0,
                'figural_serial' => 0,
                'pelayanan_publik' => 0,
                'jejaring_kerja' => 0,
                'sosial_budaya' => 0,
                'teknologi_informasi_dan_komunikasi_tik' => 0,
                'profesionalisme' => 0,
                'anti_radikalisme' => 0,
            ];
            
            foreach ($soals as $soal) {
                $soal_id = $soal->id;
                $jawaban_user = $jawabanUsers[$soal_id] ?? null; // Get user answer or null if not set
                $status = 'kosong'; // Default status for unanswered questions
        
                $tipe_key = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '_', $soal->tipe), '_'));

                if($soal->kategori == "TKP"){
                    if ($jawaban_user !== null) {
                       $status = 'benar';
                       $scores['tkp_score'] += $soal['score_'.strtolower($jawaban_user)];
                        $scores['total_benar']++;
                    } else {
                        $scores['total_kosong']++;
                    }

                    if (array_key_exists($tipe_key, $scores) && $status == 'benar') {
                        $scores[$tipe_key] += $soal['score_'.strtolower($jawaban_user)];
                    }
                } else {
                    if ($jawaban_user === $soal->jawaban_benar) {
                        $status = 'benar';
                        $scores[strtolower($soal->kategori) . '_score'] += $soal->poin;
                        $scores['total_benar']++;
                    } elseif ($jawaban_user !== null) {
                        $status = 'salah';
                        $scores['total_salah']++;
                    } else {
                        $scores['total_kosong']++;
                    }

                    if (array_key_exists($tipe_key, $scores) && $status == 'benar') {
                        $scores[$tipe_key] += $soal->poin;
                    }
                }
        
                JawabanUser::updateOrCreate(
                    ['user_id' => $user->id, 'soal_id' => $soal_id],
                    ['set_soal_id' => $set_soal_id, 'jawaban_user' => $jawaban_user, 'status' => $status]
                );
            }
            
            HasilTryout::updateOrCreate(
                ['user_id' => $user->id, 'set_soal_id' => $set_soal_id],
                $scores
            );
            
            // Periksa kategori soal latihan
            $setSoal = SetSoal::findOrFail($set_soal_id);
if ($setSoal->kategori == 'Latihan') {
    // Dapatkan kategori soal
    $firstSoal = Soal::where('set_soal_id', $set_soal_id)->first();
    $kategoriSoal = $firstSoal ? $firstSoal->kategori : null;
    
    // Tandai bahwa user telah menyelesaikan latihan untuk kategori ini
    if ($kategoriSoal) {
        // Opsional: Buat tabel atau kolom baru untuk melacak progress antar kategori
        // Atau gunakan cache/session untuk menyimpan informasi ini
        session()->flash('completed_latihan_' . strtolower($kategoriSoal), true);
        
        // Tambahkan pesan notifikasi sesuai kategori
        if ($kategoriSoal == 'TWK') {
            toast()->success('Latihan TWK selesai! Anda sekarang dapat mengakses materi TIU.');
        } else if ($kategoriSoal == 'TIU') {
            toast()->success('Latihan TIU selesai! Anda sekarang dapat mengakses materi TKP.');
        } else if ($kategoriSoal == 'TKP') {
            toast()->success('Latihan TKP selesai! Anda sekarang dapat mengakses Tryout CPNS.');
        }
    }
    
    // Periksa jika semua latihan sudah selesai
    $this->checkAllLatihanCompleted($user->id);
}
        
            return redirect()->route('tryout.result', $set_soal_id);
            
        } catch (\Exception $e) {
            // Log error
            SystemErrorController::logError(
                Auth::id(), 
                $e->getCode() ?: '500', 
                'Server Error', 
                $e->getMessage()
            );
            
            // Notify user
            toast()->error('Terjadi kesalahan saat menyimpan jawaban. Jawaban Anda telah disimpan sementara, silakan lanjutkan pengerjaan.');
            return redirect()->back();
        }
    }
    
   public function result($set_soal)
{
    try {
        $userId = Auth::user()->id;
        $hasilTryout = HasilTryout::where('user_id', $userId)->where('set_soal_id', $set_soal)->first();
        
        if(!$hasilTryout){
            toast()->error('Anda belum mengerjakan tryout!');
            return redirect()->back();
        }
        
        $soals = Soal::where('set_soal_id', $set_soal)->get();
        $hasilTryout['total_poin'] = $soals->count() * 5;
        $skbSetSoal = SetSoal::findOrFail($set_soal);
        $jawabanUsers = JawabanUser::where('user_id', $userId)->where('set_soal_id', $set_soal)->with('soal')->get();
        
        // Deteksi jenis tryout dan kategori yang ada
        $isTryoutResmi = ($skbSetSoal->kategori === 'Tryout');
        $kategoriFocus = Soal::where('set_soal_id', $set_soal)
            ->select('kategori')
            ->distinct()
            ->pluck('kategori')
            ->toArray();
        
        // Informasi tryout untuk template
        $tryoutInfo = [
            'is_tryout_resmi' => $isTryoutResmi,
            'kategori_focus' => $kategoriFocus,
            'set_soal_kategori' => $skbSetSoal->kategori,
            'set_soal_title' => $skbSetSoal->title
        ];
        
        // Generate rekomendasi materi menggunakan Content Based Filtering
        $recommendations = $this->cbfService->generateMaterialRecommendations($userId, $set_soal);
        
        // Generate video recommendations
        $videoRecommendations = $this->generateVideoRecommendations($recommendations['recommendations']);
        
        // Generate weight table untuk debugging (optional - hanya jika method ada)
        $weightTable = null;
        if (config('app.debug') && method_exists($this->cbfService, 'generateWeightTable')) {
            try {
                $weightTable = $this->cbfService->generateWeightTable($userId, $set_soal);
            } catch (\Exception $e) {
                // Ignore jika ada error di weight table
                $weightTable = null;
            }
        }
        
        return view('login.tryout.hasil', compact(
            'soals', 
            'skbSetSoal', 
            'hasilTryout', 
            'jawabanUsers',
            'recommendations',
            'videoRecommendations',
            'weightTable',
            'tryoutInfo'  // Tambahan data tryout info
        ));
        
    } catch (\Exception $e) {
        // Log error
        SystemErrorController::logError(
            Auth::id(), 
            $e->getCode() ?: '500', 
            'Server Error', 
            $e->getMessage()
        );
        
        // Notify user
        toast()->error('Terjadi kesalahan saat menampilkan hasil.');
        return redirect()->back();
    }
}

      public function pembahasan($set_soal)
    {
        try {
            $userId = Auth::user()->id;
            $hasilTryout = HasilTryout::where('user_id', $userId)->where('set_soal_id', $set_soal)->first();
            
            if(!$hasilTryout){
                toast()->error('Anda belum mengerjakan tryout!');
                return redirect()->back();
            }
            
            $soals = Soal::where('set_soal_id', $set_soal)->get();
            $skbSetSoal = SetSoal::findOrFail($set_soal);
            $jawabanUsers = JawabanUser::where('user_id', $userId)->where('set_soal_id', $set_soal)->with('soal')->get();
            
            
            return view('login.tryout.pembahasan', compact(
                'soals', 
                'skbSetSoal', 
                'jawabanUsers',
            ));
            
        } catch (\Exception $e) {
            // Log error
            SystemErrorController::logError(
                Auth::id(), 
                $e->getCode() ?: '500', 
                'Server Error', 
                $e->getMessage()
            );
            
            // Notify user
            toast()->error('Terjadi kesalahan saat menampilkan hasil.');
            return redirect()->back();
        }
    }

   private function generateVideoRecommendations(array $materialRecommendations): array
{
    $videoRecommendations = [];
    
    // Inisialisasi berdasarkan kategori yang tersedia dari recommendations
    $availableCategories = array_keys($materialRecommendations);
    
    foreach ($availableCategories as $kategori) {
        $videoRecommendations[$kategori] = [];
        
        if (!empty($materialRecommendations[$kategori])) {
            // Ambil kata kunci dari 3 materi teratas
            $keywords = [];
            foreach (array_slice($materialRecommendations[$kategori], 0, 3) as $item) {
                $materialKeywords = json_decode($item['material']->kata_kunci ?? '[]', true);
                $keywords = array_merge($keywords, array_slice($materialKeywords, 0, 2));
            }
            
            if (!empty($keywords)) {
                $videos = $this->youtubeService->searchVideosByKeywords($keywords, 3);
                $videoRecommendations[$kategori] = $videos;
            }
        }
    }

    return $videoRecommendations;
}

     /**
     * API endpoint untuk mendapatkan rekomendasi berdasarkan kategori
     */
    public function getRecommendationsByCategory(Request $request, $setSoalId, $kategori)
    {
        try {
            $userId = Auth::id();
            $recommendations = $this->cbfService->generateCategorySpecificRecommendations($userId, $setSoalId, $kategori);
            
            return response()->json([
                'success' => true,
                'data' => $recommendations
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil rekomendasi'
            ], 500);
        }
    }
    
    /**
     * Periksa apakah semua latihan sudah selesai
     */
    private function checkAllLatihanCompleted($userId)
    {
        try {
            // Dapatkan semua set soal latihan
            $latihan_sets = SetSoal::where('kategori', 'Latihan')
                ->where('status', 'Publish')
                ->pluck('id')
                ->toArray();
            
            $completed_latihan = HasilTryout::where('user_id', $userId)
                ->whereIn('set_soal_id', $latihan_sets)
                ->pluck('set_soal_id')
                ->toArray();
            
            // Jika semua latihan sudah selesai, aktifkan is_akses
            if (count($completed_latihan) >= count($latihan_sets) && count($latihan_sets) > 0) {
                $user = Auth::user();
                $user->is_akses = true;
                $user->save();
                
                return true;
            }
            
            return false;
        } catch (\Exception $e) {
            // Log error
            SystemErrorController::logError(
                $userId, 
                $e->getCode() ?: '500', 
                'Server Error', 
                $e->getMessage()
            );
            
            return false;
        }
    }
}