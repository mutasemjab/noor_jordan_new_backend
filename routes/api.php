<?php

use App\Http\Controllers\Api\Student\AnnouncementController;
use App\Http\Controllers\Api\Student\AttendanceController as StudentAttendanceController;
use App\Http\Controllers\Api\Student\ClassController;
use App\Http\Controllers\Api\Student\ContractController;
use App\Http\Controllers\Api\Student\AppSettingController;
use App\Http\Controllers\Api\Student\BannerController;
use App\Http\Controllers\Api\Student\AuthController;
use App\Http\Controllers\Api\Student\EducationalNoteController;
use App\Http\Controllers\Api\Student\ExamController;
use App\Http\Controllers\Api\Student\GradeController as StudentGradeController;
use App\Http\Controllers\Api\Student\HomeController;
use App\Http\Controllers\Api\Student\NotificationController;
use App\Http\Controllers\Api\Student\PreviousYearExamController;
use App\Http\Controllers\Api\Student\ProfileController;
use App\Http\Controllers\Api\Student\QuestionBankController;
use App\Http\Controllers\Api\Student\ScheduleController as StudentScheduleController;
use App\Http\Controllers\Api\Student\TeacherController;
use App\Http\Controllers\Api\Student\WorksheetController;

use App\Http\Controllers\Api\Teacher\AnnouncementController as TeacherAnnouncementController;
use App\Http\Controllers\Api\Teacher\AuthController as TeacherAuthController;
use App\Http\Controllers\Api\Teacher\AttendanceController as TeacherAttendanceController;
use App\Http\Controllers\Api\Teacher\ClassController as TeacherClassController;
use App\Http\Controllers\Api\Teacher\GradeController as TeacherGradeController;
use App\Http\Controllers\Api\Teacher\HomeController as TeacherHomeController;
use App\Http\Controllers\Api\Teacher\ProfileController as TeacherProfileController;
use App\Http\Controllers\Api\Teacher\ScheduleController as TeacherScheduleController;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Student Mobile API — v1
|--------------------------------------------------------------------------
*/
Route::prefix('v1/student')->middleware('api.locale')->group(function () {

    // ── Auth (public) ──────────────────────────────────────────────────────
    Route::post('auth/login', [AuthController::class, 'login']);

    // ── App settings (public) ─────────────────────────────────────────────
    Route::get('app-settings', [AppSettingController::class, 'index']);

    // ── Home ───────────────────────────────────────────────────────────────
    Route::get('home', [HomeController::class, 'index']);

    // ── Banners ───────────────────────────────────────────────────────────
    Route::get('banners', [BannerController::class, 'index']);

    // ── Teachers (public) ─────────────────────────────────────────────────
    Route::get('teachers',      [TeacherController::class, 'index']);
    Route::get('teachers/{id}', [TeacherController::class, 'show']);

    // ── Exams (public listing & detail) ───────────────────────────────────
    Route::get('exams',      [ExamController::class, 'index']);
    Route::get('exams/{id}', [ExamController::class, 'show']);

    // ── Files (public) ────────────────────────────────────────────────────
    Route::get('previous-year-exams',      [PreviousYearExamController::class, 'index']);
    Route::get('previous-year-exams/{id}', [PreviousYearExamController::class, 'show']);
    Route::get('question-banks',           [QuestionBankController::class, 'index']);
    Route::get('question-banks/{id}',      [QuestionBankController::class, 'show']);
    Route::get('worksheets',               [WorksheetController::class, 'index']);
    Route::get('worksheets/{id}',          [WorksheetController::class, 'show']);

    // ── Protected routes ──────────────────────────────────────────────────
    Route::middleware('auth:sanctum')->group(function () {

        // Auth
        Route::post('auth/logout',                   [AuthController::class, 'logout']);
        Route::delete('auth/delete-account',         [AuthController::class, 'deleteAccount']);
        Route::post('auth/switch-sibling/{sibling}', [AuthController::class, 'switchSibling']);

        // Profile
        Route::get('profile',  [ProfileController::class, 'show']);
        Route::put('profile',  [ProfileController::class, 'update']);
        Route::get('my-exams', [ProfileController::class, 'myExams']);

        // Exam flow + result retrieval
        Route::post('exams/{id}/start',          [ExamController::class, 'start']);
        Route::post('attempts/{attempt}/submit',  [ExamController::class, 'submit']);
        Route::get('attempts/{attempt}',          [ExamController::class, 'result']);

        // Class subjects, schedule & videos
        Route::get('my-subjects',                 [ClassController::class, 'mySubjects']);
        Route::get('subjects/{subjectId}/videos', [ClassController::class, 'subjectVideos']);
        Route::get('my-schedule',                 [StudentScheduleController::class, 'index']);

        // Attendance
        Route::get('my-attendance', [StudentAttendanceController::class, 'index']);

        // Grades
        Route::get('my-grades', [StudentGradeController::class, 'index']);

        // Contract & payments
        Route::get('contract', [ContractController::class, 'show']);

        // Educational notes
        Route::get('educational-notes', [EducationalNoteController::class, 'index']);

        // Announcements
        Route::get('announcements',      [AnnouncementController::class, 'index']);
        Route::get('announcements/{id}', [AnnouncementController::class, 'show']);

        // Push notifications
        Route::post('device-token',            [NotificationController::class, 'saveToken']);
        Route::get('notifications',            [NotificationController::class, 'index']);
        Route::post('notifications/read-all',  [NotificationController::class, 'markAllRead']);
        Route::post('notifications/{id}/read', [NotificationController::class, 'markRead']);
    });
});

/*
|--------------------------------------------------------------------------
| Teacher Mobile API — v1
|--------------------------------------------------------------------------
*/
Route::prefix('v1/teacher')->middleware('api.locale')->group(function () {

    // ── Auth (public) ──────────────────────────────────────────────────────
    Route::post('auth/login', [TeacherAuthController::class, 'login']);

    // ── Protected routes ──────────────────────────────────────────────────
    Route::middleware(['auth:sanctum', 'teacher.api'])->group(function () {

        // Auth
        Route::post('auth/logout', [TeacherAuthController::class, 'logout']);

        // Home / Dashboard
        Route::get('home', [TeacherHomeController::class, 'index']);

        // Profile
        Route::get('profile', [TeacherProfileController::class, 'show']);
        Route::put('profile', [TeacherProfileController::class, 'update']);

        // Device token for push notifications
        Route::post('device-token', function (\Illuminate\Http\Request $request) {
            $request->validate(['fcm_token' => ['required', 'string']]);
            $request->user()->update(['fcm_token' => $request->fcm_token]);
            return response()->json(['status' => true, 'message' => 'تم حفظ رمز الجهاز']);
        });

        // Schedule
        Route::get('my-schedule', [TeacherScheduleController::class, 'index']);

        // Classes & students
        Route::get('my-classes',               [TeacherClassController::class, 'myClasses']);
        Route::get('classes/{class}/students', [TeacherClassController::class, 'students']);

        // Attendance
        Route::get('attendance',  [TeacherAttendanceController::class, 'index']);
        Route::post('attendance', [TeacherAttendanceController::class, 'store']);

        // Grades
        Route::get('grades',  [TeacherGradeController::class, 'index']);
        Route::post('grades', [TeacherGradeController::class, 'store']);

        // Announcements (global/school-wide only)
        Route::get('announcements',      [TeacherAnnouncementController::class, 'index']);
        Route::get('announcements/{id}', [TeacherAnnouncementController::class, 'show']);
    });
});
