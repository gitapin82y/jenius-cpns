<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Paket;
use App\Models\Pembelian;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\Province;


class AuthController extends Controller
{
    // Registrasi
    public function view_register(Request $request)
    {
        $provinces = Province::all();
        return view('public.register',compact('provinces'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|numeric',
            'birth_date' => 'required|date',
            'province_id' => 'required',
            'city_id' => 'required',
            'last_education' => 'required|string|max:255',
            'major' => 'required|string|max:255',
            'password' => 'required|string|min:6|confirmed',
        ],[
            'name.required' => 'Nama lengkap wajib diisi.',
            'phone.required' => 'Nomor telepon wajib diisi.',
            'birth_date.required' => 'Tanggal lahir wajib diisi.',
            'province_id.required' => 'Provinsi wajib dipilih.',
            'city_id.required' => 'Kota/Kabupaten wajib dipilih.',
            'last_education.required' => 'Pendidikan terakhir wajib diisi.',
            'major.required' => 'Jurusan wajib diisi.',
        ]);
    
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'birth_date' => $request->birth_date,
            'province_id' => $request->province_id,
            'city_id' => $request->city_id,
            'last_education' => $request->last_education,
            'major' => $request->major,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        toast()->success('Berhasil Masuk. Selamat datang!');
        if(Auth::user()->is_admin){
            return redirect('/dashboard');
        }else{
            return redirect('/tryout');
        }
    }
        
    // Login
    public function view_login(Request $request)
    {
        return view('public.login');
    }


    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
    
        if (!Auth::attempt($request->only('email', 'password'))) {
            toast()->error('Email atau password salah.');
            return redirect()->back()->withInput();
        }
    
        $request->session()->regenerate();
    
        toast()->success('Login berhasil. Selamat datang!');
        if(Auth::user()->is_admin){
            return redirect('/dashboard');
        }else{
            return redirect('/tryout');
        }
    }

    public function view_forgotPassword(Request $request)
    {
        return view('public.forgot-password');
    }

    // Lupa Password
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        return response()->json(['message' => 'konfirmasi email reset password'], 200);
    }


    // Logout
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        toast()->success('Berhasil Logout!');
        return redirect('/login');
    }
}
