<?php

namespace App\Http\Controllers;

use App\Models\User;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Hash;
use App\Models\Paket;
use App\Models\Pembelian;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Province;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if(!Auth::user()->is_admin){
            return redirect('/');
        }
        if ($request->ajax()) {
            return DataTables::of(User::with(['paket', 'province', 'regencies'])
            ->where('is_admin', false)
            ->latest())
            ->addColumn('province', function(User $user){
                return $user->province ? $user->province->name : '-';
            })
            ->addColumn('city', function(User $user){
                return $user->regencies ? $user->regencies->name : '-';
            })
                ->addColumn('paket_id', function (User $user) {
                    return $user->paket->nama_paket;
                })
                ->addColumn('daftar_member', function (User $user) {
                    return $user->daftar_member ?? '-'; // Menggunakan null coalescing operator
                })
                ->addColumn('selesai_member', function (User $user) {
                    return $user->selesai_member ?? '-'; // Menggunakan null coalescing operator
                })
                ->addColumn('action', function ($user) {
                    return '
                        <button type="button" class="btn btn-primary btn-sm" onclick="editUser(' . htmlspecialchars(json_encode($user), ENT_QUOTES, 'UTF-8') . ')"><i class="fas fa-pen fa-sm text-white-50"></i> Edit</button>
                        <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete(' . $user->id . ')"><i class="fas fa-trash fa-sm text-white-50"></i> Hapus</button>
                    ';
                })
                ->make(true);
        }

        $pakets = Paket::all();
        $provinces = Province::all();

        return view('admin.pengguna.index',compact('pakets','provinces'));
    }


    public function public(){
        $user = Auth::user();
        if(Auth::user()){
        $paketAktif = $user->skbPakets()->wherePivot('end_date', '>=', Carbon::now())->get();
        }else{
            $paketAktif = false;
        }
        return view('public.index', compact('paketAktif'));
    }

    public function dashboard()
    {
        if(!Auth::user()->is_admin){
            return redirect()->back();
        }
        // Total penjualan
        $totalPenjualan = Pembelian::where('status', 'Approved')
        ->sum('harga');
    
        // Total penjualan bulan ini
        $totalPenjualanBulanIni = Pembelian::where('status', 'Approved')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('harga');
    
        // Penjualan per bulan untuk grafik
        $penjualanBulanan = Pembelian::where('status', 'Approved')
            ->whereYear('created_at', Carbon::now()->year)
            ->selectRaw('MONTH(created_at) as bulan, SUM(harga) as total')
            ->groupBy('bulan')
            ->pluck('total', 'bulan')
            ->toArray();
    
        $penjualanBulananFormatted = [];
        
        for ($i = 1; $i <= 12; $i++) {
            $penjualanBulananFormatted[] = $penjualanBulanan[$i] ?? 0;
        }
    
        // Total akun Paket Free
        $totalAkunFree = Pembelian::where('nama_paket', 'Free')->where('status', 'Approved')->count();
    
        // Total akun Paket Member
        $totalAkunMember =  Pembelian::where('nama_paket','!=','Free')->where('status', 'Approved')->count();
    
        // Persentase Akun
        $totalAkun = $totalAkunFree + $totalAkunMember;
        $persentaseAkunFree = $totalAkun ? ($totalAkunFree / $totalAkun) * 100 : 0;
        $persentaseAkunMember = $totalAkun ? ($totalAkunMember / $totalAkun) * 100 : 0;
        return view('admin.dashboard', compact(
            'totalPenjualan', 
            'totalPenjualanBulanIni', 
            'penjualanBulananFormatted', 
            'totalAkunFree', 
            'totalAkunMember', 
            'persentaseAkunFree', 
            'persentaseAkunMember'
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
            'birth_date' => 'required|date',
            'province_id' => 'required',
            'city_id' => 'required',
            'last_education' => 'required|string|max:255',
            'major' => 'required|string|max:255',
            'password' => 'required|min:6',
            'paket_id' => 'required|integer',
            'daftar_member' => 'nullable|date',
            'selesai_member' => 'nullable|date',
        ],[
            'phone.required' => 'Nomor telepon wajib diisi.',
            'birth_date.required' => 'Tanggal lahir wajib diisi.',
            'province_id.required' => 'Provinsi wajib dipilih.',
            'city_id.required' => 'Kota/Kabupaten wajib dipilih.',
            'last_education.required' => 'Pendidikan terakhir wajib diisi.',
            'major.required' => 'Jurusan wajib diisi.',
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
            'birth_date' => 'required|date',
            'province_id' => 'required',
            'city_id' => 'required',
            'last_education' => 'required|string|max:255',
            'major' => 'required|string|max:255',
            'paket_id' => 'required|integer',
            'daftar_member' => 'nullable|date',
            'selesai_member' => 'nullable|date',
        ],[
            'phone.required' => 'Nomor telepon wajib diisi.',
            'birth_date.required' => 'Tanggal lahir wajib diisi.',
            'province_id.required' => 'Provinsi wajib dipilih.',
            'city_id.required' => 'Kota/Kabupaten wajib dipilih.',
            'last_education.required' => 'Pendidikan terakhir wajib diisi.',
            'major.required' => 'Jurusan wajib diisi.',
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
        $user = User::with('hasilTryouts', 'jawabanUsers', 'pembelians')->findOrFail($id);

        $user->hasilTryouts()->delete();
    
        $user->jawabanUsers()->delete();
    
        $user->pembelians()->delete();
    
        // Hapus user
        $user->delete();

        return response()->json(['message' => 'Data Berhasil Dihapus!']);
    }
}
