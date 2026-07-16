<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\StudentAuthController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group([
    'prefix'     => LaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath'],
], function () {

    // ── Public front routes ───────────────────────────────────────────────
    Route::get('/',          [HomeController::class, 'index'])->name('home');
    Route::post('/contact',  [HomeController::class, 'contact'])->name('contact.store');

    Route::get('/exams',                               [HomeController::class, 'exams'])->name('exams.index');
    Route::get('/exams/{id}',                          [HomeController::class, 'examShow'])->name('exams.show');
    Route::get('/exams/{id}/take',                     [HomeController::class, 'examTake'])->name('exams.take');
    Route::post('/exams/{id}/submit',                  [HomeController::class, 'examSubmit'])->name('exams.submit');
    Route::get('/exams/{examId}/result/{attemptId}',   [HomeController::class, 'examResult'])->name('exams.result');

    // ── Student auth (guests only) ────────────────────────────────────────
    Route::middleware('guest:student')->group(function () {
        Route::get('/login',     [StudentAuthController::class, 'showLogin'])->name('student.login');
        Route::post('/login',    [StudentAuthController::class, 'login'])->name('student.login.post');
        Route::get('/register',  [StudentAuthController::class, 'showRegister'])->name('student.register');
        Route::post('/register', [StudentAuthController::class, 'register'])->name('student.register.post');
    });

    Route::post('/logout', [StudentAuthController::class, 'logout'])
        ->name('student.logout')
        ->middleware('auth:student');

});
