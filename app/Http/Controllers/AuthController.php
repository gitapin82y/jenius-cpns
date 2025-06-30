<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\RequestUserStatusMail;

class AuthController extends Controller
{
    // Registrasi
    public function view_register(Request $request)
    {
        return view('public.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ],[
            'name.required' => 'Nama lengkap wajib diisi.',
            'phone.required' => 'Nomor telepon wajib diisi.',
        ]);
    
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'status' => 'active',
            'is_review' => 0,
        ]);

        // Mail::to('apinai82y@gmail.com')->send(new RequestUserStatusMail($user));

        Alert::success('Registrasi Berhasil', 'Silakan login.');

        return redirect('/login');
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

        if (Auth::user()->status !== 'active') {
            Auth::logout();
            Alert::warning('Akun belum aktif', 'Silakan menunggu konfirmasi admin.');
            return redirect('/login');
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
