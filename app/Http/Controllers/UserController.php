<?php

namespace App\Http\Controllers;

use App\Models\User;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Http\Request;
use App\Models\HasilTryout;
use App\Models\Soal;
use App\Models\Material;
use App\Models\SetSoal;
use App\Models\UserMaterialProgress; 
use DataTables;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if(!Auth::user()->is_admin){
            return redirect('/');
        }
        if ($request->ajax()) {
            return DataTables::of(User::where('is_admin', false)
            ->latest())
                ->addColumn('action', function ($user) {
                    return '
                        <button type="button" class="btn btn-primary btn-sm" onclick="editUser(' . htmlspecialchars(json_encode($user), ENT_QUOTES, 'UTF-8') . ')"><i class="fas fa-pen fa-sm text-white-50"></i> Ubah</button>
                        <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete(' . $user->id . ')"><i class="fas fa-trash fa-sm text-white-50"></i> Hapus</button>
                    ';
                })
                ->make(true);
        }

        return view('admin.pengguna.index');
    }


    public function public(){
        return view('public.index');
    }

  
    public function dashboard()
    {
        // Count total users
        $totalUsers = User::where('is_admin', false)->count();
        
        // Count completed tryouts
        $completedTryouts = HasilTryout::distinct('user_id', 'set_soal_id')->count();
        
        // Count materials
        $totalMaterials = Material::count();
        
        // Count tryout questions
        $totalQuestions = Soal::whereHas('setSoal', function($query) {
            $query->where('kategori', 'Tryout');
        })->count();
        
        // Data untuk grafik pengguna tryout per bulan
        $tryoutData = HasilTryout::selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, COUNT(DISTINCT user_id) as total_users')
            ->whereRaw('created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)')
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();
        
        // Format data untuk grafik
        $labels = [];
        $userData = [];
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        foreach($tryoutData as $data) {
            $labels[] = $months[$data->month] . ' ' . $data->year;
            $userData[] = $data->total_users;
        }
        
        // Data untuk pie chart: Persentase pengguna yang telah menyelesaikan tryout resmi
        // Ambil semua tryout resmi (bukan latihan)
        $officialTryoutIds = SetSoal::where('kategori', 'Tryout')->pluck('id')->toArray();
        
        // Jumlah pengguna (non-admin) yang punya akses tryout
        $totalEligibleUsers = User::where('is_admin', false)->where('is_akses', true)->count();
        
        // Jumlah pengguna yang telah menyelesaikan tiap tryout resmi
        $completedTryoutData = [];
        $tryoutTitles = [];
        
        if(!empty($officialTryoutIds)) {
            $tryoutCompletionData = HasilTryout::select('set_soal_id', DB::raw('COUNT(DISTINCT user_id) as completed_users'))
                ->whereIn('set_soal_id', $officialTryoutIds)
                ->groupBy('set_soal_id')
                ->with('setSoal')
                ->get();
            
            foreach($tryoutCompletionData as $data) {
                $title = $data->setSoal ? $data->setSoal->title : 'Unknown Tryout';
                $completedCount = $data->completed_users;
                $percentage = $totalEligibleUsers > 0 ? round(($completedCount / $totalEligibleUsers) * 100, 1) : 0;
                
                $tryoutTitles[] = $title;
                $completedTryoutData[] = [
                    'title' => $title,
                    'count' => $completedCount,
                    'percentage' => $percentage
                ];
            }
        }
        
        // Jika tidak ada data, tambahkan data dummy untuk pie chart
        if(empty($completedTryoutData)) {
            $tryoutTitles[] = 'Belum Ada Data';
            $completedTryoutData[] = [
                'title' => 'Belum Ada Data',
                'count' => 0,
                'percentage' => 0
            ];
        }
        
        return view('admin.dashboard', compact(
            'totalUsers', 
            'completedTryouts', 
            'totalMaterials', 
            'totalQuestions',
            'labels',
            'userData',
            'tryoutTitles',
            'completedTryoutData',
            'totalEligibleUsers'
        ));
    }

    public function kontak(){
        return view('public.kontak');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $request->id,
            'phone' => 'required|numeric',
            'password' => 'required|min:6',
        ],[
            'name.required' => 'Nama lengkap wajib diisi.',
            'phone.required' => 'Nomor telepon wajib diisi.',
        ]);

        $validatedData['is_akses'] = 1;

        User::create($validatedData);

        return response()->json(['message' => 'Data Berhasil Disimpan!']);
    }

    public function update(Request $request, $id)
    {

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'phone' => 'required|numeric',
        ],[
            'name.required' => 'Nama lengkap wajib diisi.',
            'phone.required' => 'Nomor telepon wajib diisi.',
        ]);

        $user = User::findOrFail($id);

        if ($request->filled('password')) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        }

        $user->update($validatedData);
        
        return response()->json(['message' => 'Data Berhasil Diupdate!']);
    }

    public function destroy($id)
    {
        $user = User::with('hasilTryouts', 'jawabanUsers')->findOrFail($id);

        $user->hasilTryouts()->delete();
    
        $user->jawabanUsers()->delete();
    
        // Hapus user
        $user->delete();

        return response()->json(['message' => 'Data Berhasil Dihapus!']);
    }
}
