<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class EnsureUserIsActive
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $status = Auth::user()->status;
            if ($status !== 'active') {
                Auth::logout();
                $message = $status === 'pending'
                    ? 'Silakan menunggu konfirmasi admin.'
                    : 'Akun Anda ditolak oleh admin.';
                Alert::warning('Akses Ditolak', $message);
                return redirect('/login');
            }
        }
        return $next($request);
    }
}