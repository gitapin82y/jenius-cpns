<?php

namespace App\Http\Controllers;

use App\Models\User;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Http\Request;
use App\Models\HasilTryout;
use App\Models\Soal;
use App\Models\Material;
use DataTables;
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
                        <button type="button" class="btn btn-primary btn-sm" onclick="editUser(' . htmlspecialchars(json_encode($user), ENT_QUOTES, 'UTF-8') . ')"><i class="fas fa-pen fa-sm text-white-50"></i> Edit</button>
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
        if(!Auth::user()->is_admin){
                return redirect()->back();
            }
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
        
        return view('admin.dashboard', compact('totalUsers', 'completedTryouts', 'totalMaterials', 'totalQuestions'));
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
