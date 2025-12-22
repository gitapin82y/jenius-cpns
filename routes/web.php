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
use App\Http\Controllers\KeywordUpdateController;
use App\Http\Controllers\CBFEvaluationController;
use App\Http\Controllers\UserCBFEvaluationController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\AutomaticCBFEvaluationController;


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

Route::get('/tryout', [SetSoalController::class, 'public']);
Route::get('/materi-belajar', [MaterialController::class, 'public'])->name('public.materi.index');
Route::get('/materi-belajar/{id}', [MaterialController::class, 'show'])->name('materi-belajar.show');

Route::middleware(['auth','active'])->group(function () {

    Route::get('/dashboard', [UserController::class, 'dashboard']);

    Route::get('/logout', [AuthController::class, 'logout']);

     // Route untuk keyword suggestions
    Route::post('/materi/keyword-suggestions', [MaterialController::class, 'getKeywordSuggestions']);
    Route::post('/soal/keyword-suggestions', [SoalController::class, 'getKeywordSuggestions']);
    //  Route::post('/image-upload', [MaterialController::class, 'uploadImage'])->name('image.upload');
    
    Route::resource('/pengguna', UserController::class);
    Route::post('/pengguna/{id}', [UserController::class, 'update']);
    Route::post('/pengguna/{id}/accept', [UserController::class, 'accept']);
    Route::post('/pengguna/{id}/reject', [UserController::class, 'reject']);

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

    // Route untuk admin - materi
    Route::resource('/materi', MaterialController::class);
    Route::post('materi/change-status/{id}', [MaterialController::class, 'changeStatus']);
    Route::get('/materi/get-tipes', [MaterialController::class, 'getTipes'])->name('materi.get-tipes');

    Route::get('/tryout/{set_soal}', [TryoutController::class, 'index'])->name('tryout.index');
    Route::post('/tryout/submit', [TryoutController::class, 'submit'])->name('tryout.submit');
    Route::get('/tryout/result/{set_soal}', [TryoutController::class, 'result'])->name('tryout.result');
     Route::get('/tryout/pembahasan/{set_soal}', [TryoutController::class, 'pembahasan'])->name('tryout.pembahasan');
    
    // Route untuk Content Based Filtering API
    Route::get('/tryout/{setSoalId}/recommendations/{kategori}', [TryoutController::class, 'getRecommendationsByCategory'])
        ->name('tryout.recommendations');

    Route::post('/materi-belajar/mark-completed/{id}', [MaterialController::class, 'markCompleted'])->name('materi.mark-completed');
    Route::post('/materi-belajar/mark-all-completed/{kategori}', [MaterialController::class, 'markAllCompleted'])->name('materi.mark-all-completed');
    
    // Route untuk admin - laporan sistem
    Route::get('/system-error', [SystemErrorController::class, 'index'])->name('system-error.index');

       // === KEYWORD UPDATE ROUTES ===
    
    // Halaman update keywords
    Route::get('/admin/keyword-update', [KeywordUpdateController::class, 'showUpdatePage'])
        ->name('admin.keyword-update');
    
    // Update semua keywords (materials + soals)
    Route::post('/admin/keyword-update/all', [KeywordUpdateController::class, 'updateAllKeywords'])
        ->name('admin.keyword-update.all');
    
    // Update keywords materials saja
    Route::post('/admin/keyword-update/materials', [KeywordUpdateController::class, 'updateMaterialKeywords'])
        ->name('admin.keyword-update.materials');
    
    // Update keywords soals saja  
    Route::post('/admin/keyword-update/soals', [KeywordUpdateController::class, 'updateSoalKeywords'])
        ->name('admin.keyword-update.soals');
    
    // Update single material
    Route::post('/admin/keyword-update/material/{id}', [KeywordUpdateController::class, 'updateSingleMaterial'])
        ->name('admin.keyword-update.single-material');
    
    // Update single soal
    Route::post('/admin/keyword-update/soal/{id}', [KeywordUpdateController::class, 'updateSingleSoal'])
        ->name('admin.keyword-update.single-soal');
    
    // Preview keywords yang akan diubah
    Route::post('/admin/keyword-update/preview', [KeywordUpdateController::class, 'previewKeywords'])
        ->name('admin.keyword-update.preview');
    Route::post('/tryout/check-history', [TryoutController::class, 'checkHistory'])
    ->name('tryout.check-history');

      Route::get('/admin/cbf-evaluation', [CBFEvaluationController::class, 'dashboard'])
        ->name('admin.cbf-evaluation.dashboard');
    Route::get('/admin/cbf-evaluation/data', [CBFEvaluationController::class, 'getEvaluationData'])
        ->name('admin.cbf-evaluation.data');
    Route::get('/admin/cbf-evaluation/{id}/detail', [CBFEvaluationController::class, 'showEvaluationDetail'])
        ->name('admin.cbf-evaluation.detail');
    Route::delete('/admin/cbf-evaluation/{id}', [CBFEvaluationController::class, 'deleteEvaluation'])
        ->name('admin.cbf-evaluation.delete');
    Route::post('/admin/cbf-evaluation/bulk-delete', [CBFEvaluationController::class, 'bulkDeleteEvaluations'])
        ->name('admin.cbf-evaluation.bulk-delete');
    Route::post('/admin/cbf-evaluation/reset-user', [CBFEvaluationController::class, 'resetUserReview'])
        ->name('admin.cbf-evaluation.reset-user');
          Route::put('/admin/cbf-evaluation/{id}/update-date', [CBFEvaluationController::class, 'updateEvaluationDate'])
        ->name('admin.cbf-evaluation.update-date');

    Route::post('/user/cbf-evaluation/submit', [UserCBFEvaluationController::class, 'submitEvaluation'])
        ->name('user.cbf.evaluation.submit');

    Route::get('/user/cbf-evaluation/stats', [UserCBFEvaluationControauthller::class, 'getUserEvaluationStats'])
        ->name('user.cbf.evaluation.stats');


         Route::get('/admin/export/precision-per-user', [ExportController::class, 'exportPrecisionPerUser'])
        ->name('admin.export.precision-per-user');
    Route::get('/admin/export/cbf-evaluations', [ExportController::class, 'exportCBFEvaluations'])
        ->name('admin.export.cbf-evaluations');


        Route::get('/admin/export/pretest-posttest', [ExportController::class, 'exportPretestPosttest'])
    ->name('admin.export.pretest-posttest');


     Route::post('/tryout/{setsoal}/set-as-pretest', [TryoutController::class, 'setAsPretest'])
        ->name('tryout.set-as-pretest');
    
    Route::get('/tryout/{setsoal}/posttest', [TryoutController::class, 'posttestPage'])
        ->name('tryout.posttest');
    
    Route::get('/pretest-posttest/history', [TryoutController::class, 'pretestPosttestHistory'])
        ->name('tryout.pretest-posttest-history');

        // Tambahkan di dalam Route::middleware(['auth','active'])->group
Route::get('/admin/export/automatic-cbf-evaluations', [ExportController::class, 'exportAutomaticCBFEvaluations'])
    ->name('admin.export.automatic-cbf-evaluations');

Route::get('/admin/export/automatic-precision-per-user', [ExportController::class, 'exportAutomaticPrecisionPerUser'])
    ->name('admin.export.automatic-precision-per-user');

    Route::get('/admin/automatic-cbf-evaluation', [AutomaticCBFEvaluationController::class, 'dashboard'])
    ->name('admin.automatic-cbf-evaluation.dashboard');

Route::get('/admin/automatic-cbf-evaluation/user-data', [AutomaticCBFEvaluationController::class, 'getUserPrecisionData'])
    ->name('admin.automatic-cbf-evaluation.user-data');

Route::get('/admin/automatic-cbf-evaluation/user/{userId}/detail', [AutomaticCBFEvaluationController::class, 'getUserDetail'])
    ->name('admin.automatic-cbf-evaluation.user-detail');

        Route::post('/cbf-evaluation/submit-feedback', [UserCBFEvaluationController::class, 'submitFeedback'])
        ->name('cbf-evaluation.submit-feedback');
    
    // Get user stats
    Route::get('/cbf-evaluation/user-stats/{userId}/{setSoalId}', [UserCBFEvaluationController::class, 'getUserStats'])
        ->name('cbf-evaluation.user-stats');

        Route::get('/admin/export/user-manual-evaluations', [ExportController::class, 'exportUserManualEvaluations'])
    ->name('admin.export.user-manual-evaluations');

Route::get('/admin/export/user-manual-precision', [ExportController::class, 'exportUserManualPrecision'])
    ->name('admin.export.user-manual-precision');

});