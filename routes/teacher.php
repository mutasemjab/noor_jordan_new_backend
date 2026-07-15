<?php

use App\Http\Controllers\Teacher\DashboardController;
use App\Http\Controllers\Teacher\EducationalNoteController;
use App\Http\Controllers\Teacher\ExamController;
use App\Http\Controllers\Teacher\PreviousYearExamController;
use App\Http\Controllers\Teacher\QuestionBankController;
use App\Http\Controllers\Teacher\LoginController;
use App\Http\Controllers\Teacher\ProfileController;
use App\Http\Controllers\Teacher\WorksheetController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

/*
|--------------------------------------------------------------------------
| Teacher Panel Routes
|--------------------------------------------------------------------------
*/

Route::group([
    'prefix'     => LaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath'],
], function () {

    // ── Guest ─────────────────────────────────────────────────────────
    Route::group(['prefix' => 'teacher', 'middleware' => 'guest.teacher'], function () {
        Route::get('/login',  [LoginController::class, 'showLoginForm'])->name('teacher.showlogin');
        Route::post('/login', [LoginController::class, 'login'])->name('teacher.login');
    });

    // ── Authenticated ─────────────────────────────────────────────────
    Route::group(['prefix' => 'teacher', 'middleware' => 'auth.teacher'], function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('teacher.dashboard');
        Route::post('/logout',   [LoginController::class, 'logout'])->name('teacher.logout');

        // ── Profile ───────────────────────────────────────────────────
        Route::get('/profile',  [ProfileController::class, 'edit'])->name('teacher.profile');
        Route::post('/profile', [ProfileController::class, 'update'])->name('teacher.profile.update');

        // ── Worksheets ────────────────────────────────────────────────
        Route::resource('worksheets', WorksheetController::class, ['as' => 'teacher']);

        // ── Previous Year Exams ───────────────────────────────────────
        Route::resource('previous-year-exams', PreviousYearExamController::class, ['as' => 'teacher']);

        // ── Question Banks ────────────────────────────────────────────
        Route::resource('question-banks', QuestionBankController::class, ['as' => 'teacher']);

        // ── Educational Notes ─────────────────────────────────────────
        Route::resource('educational-notes', EducationalNoteController::class, ['as' => 'teacher']);

        // ── Exams ─────────────────────────────────────────────────────
        Route::resource('exams', ExamController::class, ['as' => 'teacher']);
        Route::post('exams/{examId}/questions',  [ExamController::class, 'storeQuestion'])->name('teacher.exams.questions.store');
        Route::delete('questions/{questionId}',  [ExamController::class, 'destroyQuestion'])->name('teacher.exams.questions.destroy');
    });

});
