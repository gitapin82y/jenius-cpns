<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Str;
use App\Models\UserMaterialProgress;
use App\Models\HasilTryout;
use App\Models\SetSoal;

class MaterialController extends Controller
{
    public function index(Request $request)
{
    if(!Auth::user()->is_admin){
        return redirect('/');
    }
    
    // Hitung jumlah materi per kategori dan total
    $countTWK = Material::where('kategori', 'TWK')->count();
    $countTIU = Material::where('kategori', 'TIU')->count();
    $countTKP = Material::where('kategori', 'TKP')->count();
    $countTotal = Material::count();
    
    if ($request->ajax()) {
        $query = Material::latest();
        
        // Filter berdasarkan kategori jika ada
        if ($request->has('kategori') && $request->kategori != '') {
            $query->where('kategori', $request->kategori);
        }
        
        // Filter berdasarkan tipe jika ada
        if ($request->has('tipe') && $request->tipe != '') {
            $query->where('tipe', $request->tipe);
        }
        
        return DataTables::of($query)
            ->addColumn('excerpt', function($material) {
                return Str::limit(strip_tags($material->content), 40, '...');
            })
            ->addColumn('action', function ($material) {
                // Action buttons code...
            })
            ->rawColumns(['action'])
            ->make(true);
    }
    
    // Ambil daftar tipe/subkategori untuk filter
    $tipes = Material::select('tipe')->distinct()->pluck('tipe');
    
    return view('admin.materi.index', compact('countTWK', 'countTIU', 'countTKP', 'countTotal', 'tipes'));
}

    public function store(Request $request)
    {
        // Map tipe to kategori
        $kategoriMap = [
            'Nasionalisme' => 'TWK',
            'Integritas' => 'TWK',
            'Bela Negara' => 'TWK',
            'Pilar Negara' => 'TWK',
            'Bahasa Indonesia' => 'TWK',
            'Verbal (Analogi)' => 'TIU',
            'Verbal (Silogisme)' => 'TIU',
            'Verbal (Analisis)' => 'TIU',
            'Numerik (Hitung Cepat)' => 'TIU',
            'Numerik (Deret Angka)' => 'TIU',
            'Numerik (Perbandingan Kuantitatif)' => 'TIU',
            'Numerik (Soal Cerita)' => 'TIU',
            'Figural (Analogi)' => 'TIU',
            'Figural (Ketidaksamaan)' => 'TIU',
            'Figural (Serial)' => 'TIU',
            'Pelayanan Publik' => 'TKP',
            'Jejaring Kerja' => 'TKP',
            'Sosial Budaya' => 'TKP',
            'Teknologi Informasi dan Komunikasi (TIK)' => 'TKP',
            'Profesionalisme' => 'TKP',
            'Anti Radikalisme' => 'TKP',
        ];
    
        $kategori = $kategoriMap[$request->input('tipe')];
        
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'tipe' => 'required|string',
            'content' => 'required|string',
        ]);
    
        $validatedData['kategori'] = $kategori;
    
        Material::create($validatedData);
    
        return response()->json(['message' => 'Materi Berhasil Disimpan!']);
    }

    public function update(Request $request, $id)
    {
        $material = Material::findOrFail($id);
        
        // Map tipe to kategori
        $kategoriMap = [
            'Nasionalisme' => 'TWK',
            'Integritas' => 'TWK',
            'Bela Negara' => 'TWK',
            'Pilar Negara' => 'TWK',
            'Bahasa Indonesia' => 'TWK',
            'Verbal (Analogi)' => 'TIU',
            'Verbal (Silogisme)' => 'TIU',
            'Verbal (Analisis)' => 'TIU',
            'Numerik (Hitung Cepat)' => 'TIU',
            'Numerik (Deret Angka)' => 'TIU',
            'Numerik (Perbandingan Kuantitatif)' => 'TIU',
            'Numerik (Soal Cerita)' => 'TIU',
            'Figural (Analogi)' => 'TIU',
            'Figural (Ketidaksamaan)' => 'TIU',
            'Figural (Serial)' => 'TIU',
            'Pelayanan Publik' => 'TKP',
            'Jejaring Kerja' => 'TKP',
            'Sosial Budaya' => 'TKP',
            'Teknologi Informasi dan Komunikasi (TIK)' => 'TKP',
            'Profesionalisme' => 'TKP',
            'Anti Radikalisme' => 'TKP',
        ];
    
        $kategori = $kategoriMap[$request->input('tipe')];
        
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'tipe' => 'required|string',
            'content' => 'required|string',
        ]);
    
        $validatedData['kategori'] = $kategori;
        
        $material->update($validatedData);
    
        return response()->json(['message' => 'Materi Berhasil Diupdate!']);
    }

    public function destroy($id)
    {
        $material = Material::findOrFail($id);
        // Delete related progress
        $material->userProgress()->delete();
        // Delete material
        $material->delete();

        return response()->json(['message' => 'Materi Berhasil Dihapus!']);
    }

    public function getTipes(Request $request)
{
    $tipes = Material::where('kategori', $request->kategori)
        ->select('tipe')
        ->distinct()
        ->pluck('tipe');
    
    return response()->json($tipes);
}
    
    public function changeStatus(Request $request, $id)
    {
        $material = Material::findOrFail($id);
        $material->status = $request->status;
        $material->save();

        return response()->json([
            'status' => true,
            'message' => 'Status Berhasil Diperbarui!'
        ]);
    }
    
    public function public()
    {
        try {
            $twkMaterials = Material::where('status', 'Publish')
                ->where('kategori', 'TWK')
                ->get()
                ->groupBy('tipe');
                
            $tiuMaterials = Material::where('status', 'Publish')
                ->where('kategori', 'TIU')
                ->get()
                ->groupBy('tipe');
                
            $tkpMaterials = Material::where('status', 'Publish')
                ->where('kategori', 'TKP')
                ->get()
                ->groupBy('tipe');
                
            // Latihan soal (set soal dengan kategori latihan)
            $twkLatihan = SetSoal::where('status', 'Publish')
                ->where('kategori', 'Latihan')
                ->whereHas('soal', function($query) {
                    $query->where('kategori', 'TWK');
                })
                ->get();
                
            $tiuLatihan = SetSoal::where('status', 'Publish')
                ->where('kategori', 'Latihan')
                ->whereHas('soal', function($query) {
                    $query->where('kategori', 'TIU');
                })
                ->get();
                
            $tkpLatihan = SetSoal::where('status', 'Publish')
                ->where('kategori', 'Latihan')
                ->whereHas('soal', function($query) {
                    $query->where('kategori', 'TKP');
                })
                ->get();
                
            // Get user progress
            $userProgress = [];
            if (Auth::check()) {
                $userId = Auth::id();
                $userProgressData = UserMaterialProgress::where('user_id', $userId)
                    ->where('is_completed', true)
                    ->pluck('material_id')
                    ->toArray();
                    
                $userProgress = array_flip($userProgressData);
                
                // Get user tryout progress
                $userTryoutProgress = HasilTryout::where('user_id', $userId)
                    ->pluck('set_soal_id')
                    ->toArray();
                
                $userTryoutProgress = array_flip($userTryoutProgress);
            }
            
            return view('login.materi.index', compact(
                'twkMaterials', 
                'tiuMaterials', 
                'tkpMaterials',
                'twkLatihan',
                'tiuLatihan',
                'tkpLatihan',
                'userProgress',
                'userTryoutProgress'
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
            toast()->error('Terjadi kesalahan saat menampilkan materi. Tim kami sudah diberitahu.');
            return redirect()->back();
        }
    }
    
   public function show($id)
{
    try {
        $material = Material::findOrFail($id);
        
        // Ambil progress user untuk materi ini
        $userProgress = null;
        if (Auth::check()) {
            $userId = Auth::id();
            $userProgress = UserMaterialProgress::where('user_id', $userId)
                ->where('material_id', $id)
                ->first();
        }
        
        // Ambil semua materi dalam tipe yang sama untuk navigasi
        $relatedMaterials = Material::where('kategori', $material->kategori)
            ->where('tipe', $material->tipe)
            ->where('status', 'Publish')
            ->get();
            
        // Ambil semua tipe lain dalam kategori yang sama
        $otherTypes = Material::where('kategori', $material->kategori)
            ->where('tipe', '!=', $material->tipe)
            ->where('status', 'Publish')
            ->select('tipe')
            ->distinct()
            ->get();
            
        // Cek progress user untuk related materials
        $completedMaterials = [];
        if (Auth::check()) {
            $userId = Auth::id();
            $completed = UserMaterialProgress::where('user_id', $userId)
                ->where('is_completed', true)
                ->pluck('material_id')
                ->toArray();
            $completedMaterials = array_flip($completed);
        }
        
        return view('login.materi.show', compact('material', 'userProgress', 'relatedMaterials', 'otherTypes', 'completedMaterials'));
    } catch (\Exception $e) {
        // Log error dan notifikasi seperti sebelumnya
    }
}
    
    public function markCompleted(Request $request, $id)
    {
        try {
            $material = Material::findOrFail($id);
            $userId = Auth::id();
            
            // Update atau buat progress
            UserMaterialProgress::updateOrCreate(
                ['user_id' => $userId, 'material_id' => $id],
                ['is_completed' => true]
            );
            
            // Cek apakah semua materi dalam kategori ini sudah selesai
            $allMaterialsInCategory = Material::where('kategori', $material->kategori)
                ->where('status', 'Publish')
                ->count();
                
            $completedMaterials = UserMaterialProgress::where('user_id', $userId)
                ->whereHas('material', function($query) use ($material) {
                    $query->where('kategori', $material->kategori)
                        ->where('status', 'Publish');
                })
                ->where('is_completed', true)
                ->count();
                
            $allCompleted = ($allMaterialsInCategory == $completedMaterials);
            
            // Jika semua materi sudah selesai, periksa latihan
            if ($allCompleted) {
                // Cek latihan yang terkait dengan kategori ini
                $latihan = SetSoal::where('kategori', 'Latihan')
                    ->whereHas('soal', function($query) use ($material) {
                        $query->where('kategori', $material->kategori);
                    })
                    ->where('status', 'Publish')
                    ->first();
                
                if ($latihan) {
                    return response()->json([
                        'status' => true,
                        'message' => 'Materi berhasil ditandai selesai!',
                        'all_completed' => true,
                        'next_step' => 'latihan',
                        'next_url' => route('tryout.index', $latihan->id)
                    ]);
                }
            }
            
            return response()->json([
                'status' => true,
                'message' => 'Materi berhasil ditandai selesai!',
                'all_completed' => $allCompleted
            ]);
        } catch (\Exception $e) {
            // Log error
            SystemErrorController::logError(
                Auth::id(), 
                $e->getCode() ?: '500', 
                'Server Error', 
                $e->getMessage()
            );
            
            // Return error response
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan, silakan coba lagi nanti.'
            ], 500);
        }
    }
}