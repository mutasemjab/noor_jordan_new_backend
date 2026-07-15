<?php

use App\Http\Controllers\Api\Student\AnnouncementController;
use App\Http\Controllers\Api\Student\ClassController;
use App\Http\Controllers\Api\Student\ContractController;
use App\Http\Controllers\Api\Student\AppSettingController;
use App\Http\Controllers\Api\Student\BannerController;
use App\Http\Controllers\Api\Student\AuthController;
use App\Http\Controllers\Api\Student\EducationalNoteController;
use App\Http\Controllers\Api\Student\ExamController;
use App\Http\Controllers\Api\Student\HomeController;
use App\Http\Controllers\Api\Student\NotificationController;
use App\Http\Controllers\Api\Student\PreviousYearExamController;
use App\Http\Controllers\Api\Student\ProfileController;
use App\Http\Controllers\Api\Student\QuestionBankController;
use App\Http\Controllers\Api\Student\TeacherController;
use App\Http\Controllers\Api\Student\WorksheetController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Student Mobile API — v1
|--------------------------------------------------------------------------
| Base URL : /api/v1/student/...
| Auth     : Laravel Sanctum — Bearer token
| Locale   : Accept-Language: ar|en  (default: ar)
|
| Response format:
|   { "status": true|false, "message": "...", "data": {...} }
|   Paginated: adds "pagination": { current_page, last_page, per_page, total }
|--------------------------------------------------------------------------
*/

Route::prefix('v1/student')->middleware('api.locale')->group(function () {

    // ── Auth (public) ──────────────────────────────────────────────────────
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login',    [AuthController::class, 'login']);

    // ── App settings (public — no auth) ───────────────────────────────────
    Route::get('app-settings', [AppSettingController::class, 'index']);

    // ── Home ───────────────────────────────────────────────────────────────
    Route::get('home', [HomeController::class, 'index']);

    // ── Banners (slider images — no auth needed) ───────────────────────────
    Route::get('banners', [BannerController::class, 'index']);

    // ── Teachers ───────────────────────────────────────────────────────────
    Route::get('teachers',      [TeacherController::class, 'index']);
    Route::get('teachers/{id}', [TeacherController::class, 'show']);

    // ── Exams (public list + detail) ───────────────────────────────────────
    Route::get('exams',      [ExamController::class, 'index']);
    Route::get('exams/{id}', [ExamController::class, 'show']);

    // ── Files (3 types: previous_year_exams / question_banks / worksheets) ─
    Route::get('previous-year-exams',       [PreviousYearExamController::class, 'index']);
    Route::get('previous-year-exams/{id}',  [PreviousYearExamController::class, 'show']);
    Route::get('question-banks',            [QuestionBankController::class, 'index']);
    Route::get('question-banks/{id}',       [QuestionBankController::class, 'show']);
    Route::get('worksheets',                [WorksheetController::class, 'index']);
    Route::get('worksheets/{id}',           [WorksheetController::class, 'show']);

    // ── Protected routes (require Bearer token) ────────────────────────────
    Route::middleware('auth:sanctum')->group(function () {

        // Auth
        Route::post('auth/logout',                    [AuthController::class, 'logout']);
        Route::delete('auth/delete-account',          [AuthController::class, 'deleteAccount']);
        Route::post('auth/switch-sibling/{sibling}',  [AuthController::class, 'switchSibling']);

        // Profile
        Route::get('profile', [ProfileController::class, 'show']);
        Route::put('profile', [ProfileController::class, 'update']);

        // My exam history
        Route::get('my-exams', [ProfileController::class, 'myExams']);

        // Exam flow
        Route::post('exams/{id}/start',          [ExamController::class, 'start']);
        Route::post('attempts/{attempt}/submit', [ExamController::class, 'submit']);

        // Class subjects & teachers
        Route::get('my-subjects', [ClassController::class, 'mySubjects']);

        // Contract & payments
        Route::get('contract', [ContractController::class, 'show']);

        // Educational notes
        Route::get('educational-notes', [EducationalNoteController::class, 'index']);

        // Announcements
        Route::get('announcements',      [AnnouncementController::class, 'index']);
        Route::get('announcements/{id}', [AnnouncementController::class, 'show']);

        // Push notifications
        Route::post('device-token',               [NotificationController::class, 'saveToken']);
        Route::get('notifications',               [NotificationController::class, 'index']);
        Route::post('notifications/read-all',     [NotificationController::class, 'markAllRead']);
        Route::post('notifications/{id}/read',    [NotificationController::class, 'markRead']);
    });
});
