<?php

use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CardController;
use App\Http\Controllers\Admin\CardNumberController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\CourseContentController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\EnrollmentController;
use App\Http\Controllers\Admin\ExamController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\PosController;
use App\Http\Controllers\Admin\PreviousYearExamController;
use App\Http\Controllers\Admin\QuestionBankController;
use App\Http\Controllers\Admin\WorksheetController;
use App\Http\Controllers\Admin\EducationalNoteController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\TeacherController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Spatie\Permission\Models\Permission;

Route::group(['prefix' => LaravelLocalization::setLocale(), 'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']], function () {

    Route::group(['prefix' => 'admin', 'middleware' => 'auth:admin'], function () {

        // ── Dashboard ─────────────────────────────────────────────────
        Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('logout', [LoginController::class, 'logout'])->name('admin.logout');

        // ── Admin profile ─────────────────────────────────────────────
        Route::get('/admin/edit/{id}',    [LoginController::class, 'editlogin'])->name('admin.login.edit');
        Route::post('/admin/update/{id}', [LoginController::class, 'updatelogin'])->name('admin.login.update');

        // ── Roles & Employees ─────────────────────────────────────────
        Route::resource('employee', EmployeeController::class, ['as' => 'admin']);
        Route::get('role',               [RoleController::class, 'index'])->name('admin.role.index');
        Route::get('role/create',        [RoleController::class, 'create'])->name('admin.role.create');
        Route::get('role/{id}/edit',     [RoleController::class, 'edit'])->name('admin.role.edit');
        Route::patch('role/{id}',        [RoleController::class, 'update'])->name('admin.role.update');
        Route::post('role',              [RoleController::class, 'store'])->name('admin.role.store');
        Route::post('admin/role/delete', [RoleController::class, 'delete'])->name('admin.role.delete');

        Route::get('/permissions/{guard_name}', function ($guard_name) {
            return response()->json(Permission::where('guard_name', $guard_name)->get());
        });

        // ── Courses ───────────────────────────────────────────────────
        Route::resource('courses', CourseController::class, ['as' => 'admin']);

        // ── Previous Year Exam ───────────────────────────────────────────────────
        Route::resource('previous-year-exams', PreviousYearExamController::class, ['as' => 'admin']);
    
        // ── Question Bank ───────────────────────────────────────────────────
        Route::resource('question-banks', QuestionBankController::class, ['as' => 'admin']);

        // ── Worksheets ──────────────────────────────────────────────────────
        Route::resource('worksheets', WorksheetController::class, ['as' => 'admin']);

        // ── Educational Notes ───────────────────────────────────────────────
        Route::resource('educational-notes', EducationalNoteController::class, ['as' => 'admin']);

        // ── Teachers ──────────────────────────────────────────────────
        Route::get('teachers/export',  [TeacherController::class, 'export'])->name('admin.teachers.export');
        Route::post('teachers/import', [TeacherController::class, 'import'])->name('admin.teachers.import');
        Route::resource('teachers', TeacherController::class, ['as' => 'admin']);

        // ── Students ──────────────────────────────────────────────────
        Route::get('students/export',  [StudentController::class, 'export'])->name('admin.students.export');
        Route::post('students/import', [StudentController::class, 'import'])->name('admin.students.import');
        Route::resource('students', StudentController::class, ['as' => 'admin']);
        Route::post('students/{student}/reset-device', [StudentController::class, 'resetDevice'])->name('admin.students.reset-device');

        // ── Categories (tree) ─────────────────────────────────────────
        Route::get('categories/{id}/children', [CategoryController::class, 'children'])->name('admin.categories.children');
        Route::resource('categories', CategoryController::class, ['as' => 'admin', 'except' => ['show']]);

        // ── Exams ─────────────────────────────────────────────────────
        Route::get('courses/{id}/exam-structure',   [ExamController::class, 'getCourseStructure'])->name('admin.courses.exam-structure');
        Route::resource('exams', ExamController::class, ['as' => 'admin']);
        Route::post('exams/{examId}/questions',     [ExamController::class, 'storeQuestion'])->name('admin.exams.questions.store');
        Route::delete('questions/{questionId}',     [ExamController::class, 'destroyQuestion'])->name('admin.exams.questions.destroy');

        // ── Cities ────────────────────────────────────────────────────
        Route::resource('cities', CityController::class, ['as' => 'admin']);

        // ── Points of Sale ────────────────────────────────────────────
        Route::resource('pos', PosController::class, ['as' => 'admin']);

        // ── Cards ─────────────────────────────────────────────────────
        Route::resource('cards', CardController::class, ['as' => 'admin']);

        // ── Card Numbers ──────────────────────────────────────────────
        Route::post('card-numbers/bulk-generate', [CardNumberController::class, 'bulkGenerate'])->name('admin.card-numbers.bulk');
        Route::get('card-numbers/print',          [CardNumberController::class, 'printView'])->name('admin.card-numbers.print');
        Route::resource('card-numbers', CardNumberController::class, ['as' => 'admin']);

        // ── Subjects ──────────────────────────────────────────────────
        Route::resource('subjects', SubjectController::class, ['as' => 'admin']);

        // ── Enrollments / Activations ─────────────────────────────────
        Route::get('enrollments', [EnrollmentController::class, 'index'])->name('admin.enrollments.index');
        Route::patch('enrollments/{enrollment}/toggle', [EnrollmentController::class, 'toggleActive'])->name('admin.enrollments.toggle');
        Route::delete('enrollments/{enrollment}', [EnrollmentController::class, 'destroy'])->name('admin.enrollments.destroy');

        // ── Course Content (Units / Lessons / Materials) ──────────────
        Route::post('courses/{id}/units',     [CourseContentController::class, 'storeUnit'])->name('admin.courses.units.store');
        Route::delete('units/{id}',           [CourseContentController::class, 'destroyUnit'])->name('admin.courses.units.destroy');
        Route::post('units/{id}/lessons',     [CourseContentController::class, 'storeLesson'])->name('admin.courses.lessons.store');
        Route::put('lessons/{id}',            [CourseContentController::class, 'updateLesson'])->name('admin.courses.lessons.update');
        Route::delete('lessons/{id}',         [CourseContentController::class, 'destroyLesson'])->name('admin.courses.lessons.destroy');
        Route::post('units/{id}/materials',   [CourseContentController::class, 'storeMaterial'])->name('admin.courses.materials.store');
        Route::delete('materials/{id}',       [CourseContentController::class, 'destroyMaterial'])->name('admin.courses.materials.destroy');

        // ── Banners ───────────────────────────────────────────────────
        Route::post('banners/{banner}/toggle', [BannerController::class, 'toggleActive'])->name('admin.banners.toggle');
        Route::resource('banners', BannerController::class, ['as' => 'admin']);

        // ── Announcements ─────────────────────────────────────────────
        Route::resource('announcements', AnnouncementController::class, ['as' => 'admin']);

        // ── Push Notifications ────────────────────────────────────────
        Route::get('notifications/send',  [NotificationController::class, 'sendForm'])->name('admin.notifications.send');
        Route::post('notifications/send', [NotificationController::class, 'send'])->name('admin.notifications.send.post');

        // ── Site Settings ─────────────────────────────────────────────
        Route::get('site-settings',           [SiteSettingController::class, 'edit'])->name('admin.site-settings.edit');
        Route::put('site-settings',           [SiteSettingController::class, 'update'])->name('admin.site-settings.update');
        Route::post('site-settings/toggle-price-display', [SiteSettingController::class, 'togglePriceDisplay'])->name('admin.site-settings.toggle-price');

        // ── Contact Messages ──────────────────────────────────────────
        Route::get('contact-messages',              [ContactMessageController::class, 'index'])->name('admin.contact_messages.index');
        Route::get('contact-messages/{contactMessage}', [ContactMessageController::class, 'show'])->name('admin.contact_messages.show');
        Route::post('contact-messages/{contactMessage}/reply', [ContactMessageController::class, 'reply'])->name('admin.contact_messages.reply');
        Route::delete('contact-messages/{contactMessage}', [ContactMessageController::class, 'destroy'])->name('admin.contact_messages.destroy');
    });
});

Route::group(['namespace' => 'Admin', 'prefix' => 'admin', 'middleware' => 'guest:admin'], function () {
    Route::get('login',  [LoginController::class, 'show_login_view'])->name('admin.showlogin');
    Route::post('login', [LoginController::class, 'login'])->name('admin.login');
});
