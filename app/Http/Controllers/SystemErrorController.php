<?php

namespace App\Http\Controllers;

use App\Models\SystemError;
use App\Models\User;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Auth;

class SystemErrorController extends Controller
{
    public function index(Request $request)
    {
        if(!Auth::user()->is_admin){
            return redirect('/');
        }
        
        if ($request->ajax()) {
            return DataTables::of(SystemError::with('user')->latest())
                ->addColumn('username', function ($error) {
                    return $error->user ? $error->user->name : 'Deleted User';
                })
                ->addColumn('formatted_time', function ($error) {
                    return $error->error_time ? date('d M Y H:i:s', strtotime($error->error_time)) : '-';
                })
                ->make(true);
        }

        return view('admin.system-error.index');
    }
    
    // Method untuk menyimpan error
    public static function logError($userId, $errorCode, $errorType, $errorMessage)
    {
        try {
            SystemError::create([
                'user_id' => $userId,
                'error_code' => $errorCode,
                'error_type' => $errorType,
                'error_message' => $errorMessage,
                'error_time' => now()
            ]);
            
            return true;
        } catch (\Exception $e) {
            // Jika gagal menyimpan error, kita akan log ke laravel.log
            \Log::error('Failed to log system error: ' . $e->getMessage());
            return false;
        }
    }
}