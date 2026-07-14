<?php

use App\Http\Controllers\Teacher\CourseController;
use App\Http\Controllers\Teacher\DashboardController;
use App\Http\Controllers\Teacher\EducationalNoteController;
use App\Http\Controllers\Teacher\ExamController;
use App\Http\Controllers\Teacher\PreviousYearExamController;
use App\Http\Controllers\Teacher\QuestionBankController;
use App\Http\Controllers\Teacher\LoginController;
use App\Http\Controllers\Teacher\ProfileController;
use App\Http\Controllers\Teacher\UnitController;
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

        // ── Courses ───────────────────────────────────────────────────
        Route::resource('courses', CourseController::class, ['as' => 'teacher']);

        // ── Units (under a course) ────────────────────────────────────
        Route::post('courses/{courseId}/units',                          [UnitController::class, 'store'])->name('teacher.courses.units.store');
        Route::put('courses/{courseId}/units/{unitId}',                  [UnitController::class, 'update'])->name('teacher.courses.units.update');
        Route::delete('courses/{courseId}/units/{unitId}',               [UnitController::class, 'destroy'])->name('teacher.courses.units.destroy');

        // ── Lessons (under a unit) ────────────────────────────────────
        Route::post('courses/{courseId}/units/{unitId}/lessons',         [UnitController::class, 'storeLesson'])->name('teacher.lessons.store');
        Route::put('courses/{courseId}/units/{unitId}/lessons/{lessonId}', [UnitController::class, 'updateLesson'])->name('teacher.lessons.update');
        Route::delete('courses/{courseId}/units/{unitId}/lessons/{lessonId}', [UnitController::class, 'destroyLesson'])->name('teacher.lessons.destroy');

        // ── Materials (under a unit) ──────────────────────────────────
        Route::post('courses/{courseId}/units/{unitId}/materials',           [UnitController::class, 'storeMaterial'])->name('teacher.materials.store');
        Route::delete('courses/{courseId}/units/{unitId}/materials/{materialId}', [UnitController::class, 'destroyMaterial'])->name('teacher.materials.destroy');

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
        Route::post('exams/{examId}/questions',    [ExamController::class, 'storeQuestion'])->name('teacher.exams.questions.store');
        Route::delete('questions/{questionId}',    [ExamController::class, 'destroyQuestion'])->name('teacher.exams.questions.destroy');
    });

});
