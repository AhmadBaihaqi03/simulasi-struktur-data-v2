<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DashboardSesiIndividuController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\PblGroupSessionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;

use App\Http\Controllers\IndividualSessionController;


use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
}) ->name('beranda');

Route::prefix('individu')->group(function () {
    Route::post('/join', [StudentController::class, 'checkIndividualSession'])->name('student.individual.check');
    Route::get('/hasil/{submission}', [StudentController::class, 'showIndividualResult'])->name('student.individual.result');
    Route::get('/hasil/{submission}/download', [StudentController::class, 'downloadIndividualResult'])->name('student.individual.download');
    Route::get('/{session_code}', [StudentController::class, 'showIndividualQuiz'])->name('student.individual.quiz');
    Route::post('/{session_code}/submit', [StudentController::class, 'submitIndividualQuiz'])->name('student.individual.submit');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Rute untuk dashboard utama guru
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard-individu', [DashboardSesiIndividuController::class, 'index'])->name('dashboard.individu');

    // Rute untuk manajemen profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rute untuk manajemen sesi kelompok pada dashboard
    Route::get('/sessions/create', [PblGroupSessionController::class, 'create'])->name('sessions.create');
    Route::post('/sessions', [PblGroupSessionController::class, 'store'])->name('sessions.store');
    Route::get('/sessions/{session}/edit', [PblGroupSessionController::class, 'edit'])->name('sessions.edit');
    Route::put('/sessions/{session}', [PblGroupSessionController::class, 'update'])->name('sessions.update');
    Route::patch('/sessions/{session}/toggle', [PblGroupSessionController::class, 'toggle'])->name('sessions.toggle');
    Route::delete('/sessions/{session}', [PblGroupSessionController::class, 'destroy'])->name('sessions.destroy');
    Route::post('/check-member-name', [StudentController::class, 'checkMemberName'])->name('student.check.name');

    // Rute Evaluasi
    Route::get('/sessions/{session}/evaluations', [PblGroupSessionController::class, 'evaluations'])->name('sessions.evaluations');
    Route::get('/groups/{group}/work', [EvaluationController::class, 'show'])->name('groups.work');
    Route::post('/groups/{group}/evaluate', [EvaluationController::class, 'store'])->name('groups.evaluate');
    Route::post('/groups/{group}/evaluation', [EvaluationController::class, 'storeAjax'])->name('groups.evaluation.ajax');

    // Rute untuk manajemen sesi individu pada dashboard
    Route::resource('sesi-individu', IndividualSessionController::class);
   
    Route::get('/sesi-individu/{session}/nilai', [IndividualSessionController::class, 'showSubmissions'])
    ->name('sesi-individu.nilai');
    Route::get('/sesi-individu/{submission}/nilai/detail', [IndividualSessionController::class, 'reviewSubmission'])
    ->name('sesi-individu.nilai.detail');
    Route::patch('/sesi-individu/{session}/toggle', [IndividualSessionController::class, 'toggle'])->name('sesi-individu.toggle');

});

require __DIR__.'/auth.php';

Route::prefix('pbl')->group(function () {

    // Pintu Masuk
    Route::get('/join', [StudentController::class, 'showJoinForm'])->name('student.join');
    Route::post('/join', [StudentController::class, 'checkSession'])->name('student.join.check');

    // Join Group
    Route::post('/{session_code}/join-group', [StudentController::class, 'joinGroup'])->name('student.join.group');

    // Orientasi
    Route::get('/{session_code}/orientasi', [StudentController::class, 'showOrientasi'])->name('student.orientasi');

    // WORKSPACE (TANPA PHASE)
    Route::get('/{session_code}/workspace/{group_id}', [StudentController::class, 'showWorkspace'])->name('student.workspace');

    // Save All
    Route::post('/{session_code}/workspace/{group_id}/save-all', [StudentController::class, 'saveAll'])->name('student.save.all');

    // Complete
    Route::get('/{session_code}/complete/{group_id}', [StudentController::class, 'complete'])->name('student.complete');
});

// Halaman Materi
Route::get('/materi/{slug}', [MateriController::class, 'show'])->name('materi.show');
