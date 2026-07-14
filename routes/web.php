<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\StudentAuthController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group([
    'prefix'     => LaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath'],
], function () {

    // ── Public front routes ───────────────────────────────────────────────
    Route::get('/',               [HomeController::class, 'index'])->name('home');
    Route::post('/contact',       [HomeController::class, 'contact'])->name('contact.store');
    Route::get('/courses',        [HomeController::class, 'courses'])->name('courses.index');
    Route::get('/courses/{id}', [HomeController::class, 'courseDetail'])->name('courses.show');
    Route::post('/courses/{id}/activate', [HomeController::class, 'activateCourse'])->name('courses.activate');
    Route::get('/exams',                           [HomeController::class, 'exams'])->name('exams.index');
    Route::get('/exams/{id}',                      [HomeController::class, 'examShow'])->name('exams.show');
    Route::get('/exams/{id}/take',                 [HomeController::class, 'examTake'])->name('exams.take');
    Route::post('/exams/{id}/submit',              [HomeController::class, 'examSubmit'])->name('exams.submit');
    Route::get('/exams/{examId}/result/{attemptId}', [HomeController::class, 'examResult'])->name('exams.result');
    Route::get('/teachers/{id}',                   [HomeController::class, 'teacherProfile'])->name('teachers.show');

    // ── Cart ────────────────────────────────────────────────────────────────
    Route::get('/cart',                [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{id}',      [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/remove/{id}',   [CartController::class, 'remove'])->name('cart.remove');
    Route::get('/cart/checkout',       [CartController::class, 'checkout'])->name('cart.checkout');
    Route::post('/cart/activate',      [CartController::class, 'activate'])->name('cart.activate');

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
