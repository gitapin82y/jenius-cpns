<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SoalController;
use App\Http\Controllers\SetSoalController;
use App\Http\Controllers\PaketController;
use App\Http\Controllers\HasilTryoutController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TryoutController;
use App\Http\Controllers\DependantDropdownController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\SystemErrorController;

Route::get('/', [UserController::class, 'public']);
Route::get('/kontak', [UserController::class, 'kontak']);

Route::post('send-mail',[MailController::class,'sendMail']);
Route::post('send-feedback',[MailController::class,'sendReview']);

Route::post('/register', [AuthController::class, 'register']);
Route::get('/register', [AuthController::class, 'view_register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/login', [AuthController::class, 'view_login'])->name('login');
Route::get('/forgot-password', [AuthController::class, 'view_forgotPassword']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);

Route::post('/formulir',[AuthController::class, 'formulir'])->name('formulir');

Route::get('/user/{id}', [UserController::class, 'show']);


Route::get('provinces', [DependantDropdownController::class,'provinces'])->name('provinces');
Route::get('cities', [DependantDropdownController::class,'cities'])->name('cities');

Route::get('/tryout', [SetSoalController::class, 'public']);

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [UserController::class, 'dashboard']);

    Route::get('/logout', [AuthController::class, 'logout']);
    
    Route::resource('/pengguna', UserController::class);
    Route::post('/pengguna/{id}', [UserController::class, 'update']);

    Route::get('/setsoal/counts', [SetSoalController::class, 'getCounts'])->name('setsoal.counts');
    Route::resource('/setsoal', SetSoalController::class);
    Route::post('/setsoal/{id}', [SetSoalController::class, 'update']);
    Route::post('setsoal/change-status/{id}', [SetSoalController::class, 'changeStatus']);

    Route::get('soal/{id}', [SoalController::class, 'index'])->name('soal.index');
    Route::resource('soal', SoalController::class)->except(['show','index']);
    Route::post('/soal/{id}', [SoalController::class, 'update']);
    Route::get('/soal', function () {
        return redirect()->back();
    });

        // Route untuk admin
    Route::resource('/materi', MaterialController::class);
    Route::post('materi/change-status/{id}', [MaterialController::class, 'changeStatus']);
    

    Route::get('/tryout/{set_soal}', [TryoutController::class, 'index'])->name('tryout.index');
    Route::post('/tryout/submit', [TryoutController::class, 'submit'])->name('tryout.submit');
    Route::get('/tryout/result/{set_soal}', [TryoutController::class, 'result'])->name('tryout.result');

    Route::get('/materi-belajar', [MaterialController::class, 'public'])->name('public.materi.index');
    Route::get('/materi-belajar/{id}', [MaterialController::class, 'show'])->name('materi.show');
    Route::post('/materi-belajar/mark-completed/{id}', [MaterialController::class, 'markCompleted'])->name('materi.mark-completed');
    

    // Route untuk admin - laporan sistem
    Route::get('/system-error', [SystemErrorController::class, 'index'])->name('system-error.index');
});
