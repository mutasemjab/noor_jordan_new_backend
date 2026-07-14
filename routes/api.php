<?php

use App\Http\Controllers\Api\Student\AnnouncementController;
use App\Http\Controllers\Api\Student\AppSettingController;
use App\Http\Controllers\Api\Student\BannerController;
use App\Http\Controllers\Api\Student\AuthController;
use App\Http\Controllers\Api\Student\CategoryController;
use App\Http\Controllers\Api\Student\CourseActivationController;
use App\Http\Controllers\Api\Student\CourseController;
use App\Http\Controllers\Api\Student\EducationalNoteController;
use App\Http\Controllers\Api\Student\ExamController;
use App\Http\Controllers\Api\Student\HomeController;
use App\Http\Controllers\Api\Student\LessonController;
use App\Http\Controllers\Api\Student\LessonProgressController;
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

    // ── Banners (slider images — no auth needed) ────────────────────────────
    Route::get('banners', [BannerController::class, 'index']);

    // ── Category tree navigation ───────────────────────────────────────────
    Route::get('categories',        [CategoryController::class, 'index']);
    Route::get('categories/{id}',   [CategoryController::class, 'show']);
    Route::get('subjects/{id}',     [CategoryController::class, 'subject']);

    // ── Courses ────────────────────────────────────────────────────────────
    Route::get('courses',       [CourseController::class, 'index']);
    Route::get('courses/{id}',  [CourseController::class, 'show']);

    // ── Course units + lesson content (auth optional — needed for locked check) ──
    // GET /courses/{id}/units        → units + lessons list (locked/free based on enrollment)
    // GET /lessons/{id}              → lesson detail (video_url, file_url) — 403 if locked
    // GET /units/{id}/exams          → exams attached to a unit
    Route::get('courses/{id}/units', [LessonController::class, 'courseUnits'])->middleware('auth:sanctum');
    Route::get('lessons/{id}',       [LessonController::class, 'show'])->middleware('auth:sanctum');
    Route::get('units/{id}/exams',   [LessonController::class, 'unitExams'])->middleware('auth:sanctum');

    // ── Teachers ───────────────────────────────────────────────────────────
    Route::get('teachers',      [TeacherController::class, 'index']);
    Route::get('teachers/{id}', [TeacherController::class, 'show']);

    // ── Exams (public list + detail) ───────────────────────────────────────
    Route::get('exams',      [ExamController::class, 'index']);
    Route::get('exams/{id}', [ExamController::class, 'show']);

    // ── Files (3 types: previous_year_exams / question_banks / worksheets) ─
    // Filters: subject_id, year (pye+ws), search
    Route::get('previous-year-exams',       [PreviousYearExamController::class, 'index']);
    Route::get('previous-year-exams/{id}',  [PreviousYearExamController::class, 'show']);
    Route::get('question-banks',            [QuestionBankController::class, 'index']);
    Route::get('question-banks/{id}',       [QuestionBankController::class, 'show']);
    Route::get('worksheets',                [WorksheetController::class, 'index']);
    Route::get('worksheets/{id}',           [WorksheetController::class, 'show']);

    // ── Protected routes (require Bearer token) ────────────────────────────
    Route::middleware('auth:sanctum')->group(function () {

        // Auth
        Route::post('auth/logout',          [AuthController::class, 'logout']);
        Route::delete('auth/delete-account', [AuthController::class, 'deleteAccount']);

        // Profile
        Route::get('profile', [ProfileController::class, 'show']);
        Route::put('profile', [ProfileController::class, 'update']);

        // My courses (enrolled)
        Route::get('my-courses', [ProfileController::class, 'myCourses']);

        // My exam history
        Route::get('my-exams', [ProfileController::class, 'myExams']);

        // Exam flow
        Route::post('exams/{id}/start',          [ExamController::class, 'start']);
        Route::post('attempts/{attempt}/submit', [ExamController::class, 'submit']);

        // Course activation via card code
        // POST /courses/{id}/activate   body: { card_code: "XXXX-XXXX" }
        Route::post('courses/{id}/activate', [CourseActivationController::class, 'activate']);

        // Lesson progress
        // POST /lessons/{id}/progress   body: { watch_seconds: 340, is_completed: true }
        // GET  /courses/{id}/my-progress
        Route::post('lessons/{id}/progress',       [LessonProgressController::class, 'update']);
        Route::get('courses/{id}/my-progress',     [LessonProgressController::class, 'courseProgress']);

        // Educational notes (المفكرة التعليمية — filtered by student's class)
        Route::get('educational-notes', [EducationalNoteController::class, 'index']);

        // Announcements (الإعلانات — filtered by student's class or global)
        Route::get('announcements',      [AnnouncementController::class, 'index']);
        Route::get('announcements/{id}', [AnnouncementController::class, 'show']);

        // Push notifications
        // POST /device-token          body: { fcm_token: "..." }
        // GET  /notifications         → list + unread_count
        // POST /notifications/{id}/read
        // POST /notifications/read-all
        Route::post('device-token',               [NotificationController::class, 'saveToken']);
        Route::get('notifications',               [NotificationController::class, 'index']);
        Route::post('notifications/read-all',     [NotificationController::class, 'markAllRead']);
        Route::post('notifications/{id}/read',    [NotificationController::class, 'markRead']);
    });
});
