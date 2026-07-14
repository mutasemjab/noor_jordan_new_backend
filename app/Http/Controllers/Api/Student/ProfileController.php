<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Enrollment;
use App\Models\SchoolClass;
use App\Services\ProgressService;
use App\Services\StatsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    use ApiResponse;

    public function __construct(
        private ProgressService $progress,
        private StatsService $stats,
    ) {}

    // GET /profile
    public function show(Request $request): JsonResponse
    {
        $student = $request->user()->load('schoolClass');

        return $this->success([
            'id'             => $student->id,
            'name'           => $student->name,
            'national_id'    => $student->national_id,
            'email'          => $student->email,
            'phone'          => $student->phone,
            'gender'         => $student->gender,
            'date_of_birth'  => $student->date_of_birth?->format('Y-m-d'),
            'nationality'    => $student->nationality,
            'avatar'         => $student->avatar ? asset('assets/uploads/students/' . $student->avatar) : null,
            'class'          => $student->schoolClass?->name,
            'class_id'       => $student->class_id,
            'is_active'      => $student->is_active,
            'created_at'     => $student->created_at?->format('Y-m-d'),
            'stats'          => $this->stats->studentStats($student->id),
        ]);
    }

    // PUT /profile
    public function update(Request $request): JsonResponse
    {
        $student = $request->user();

        $validated = $request->validate([
            'name'          => ['sometimes', 'string', 'max:200'],
            'national_id'   => ['sometimes', 'nullable', 'string', 'max:50', 'unique:students,national_id,' . $student->id],
            'email'         => ['sometimes', 'nullable', 'email', 'max:200', 'unique:students,email,' . $student->id],
            'phone'         => ['sometimes', 'nullable', 'string', 'max:20'],
            'gender'        => ['sometimes', 'nullable', 'in:male,female'],
            'date_of_birth' => ['sometimes', 'nullable', 'date'],
            'nationality'   => ['sometimes', 'nullable', 'string', 'max:100'],
            'class_id'      => ['sometimes', 'nullable', 'exists:classes,id'],
            'avatar'        => ['sometimes', 'nullable', 'image', 'max:2048'],
            'password'      => ['sometimes', 'confirmed', Password::min(8)],
            'current_password' => ['required_with:password'],
        ]);

        if (isset($validated['password'])) {
            if (! \Illuminate\Support\Facades\Hash::check($request->current_password, $student->password)) {
                return $this->error('كلمة المرور الحالية غير صحيحة', 422);
            }
        }

        if ($request->hasFile('avatar')) {
            $validated['avatar'] = uploadImage('assets/uploads/students', $request->file('avatar'));
        }

        unset($validated['current_password']);
        $student->update($validated);

        return $this->success([
            'id'            => $student->id,
            'name'          => $student->name,
            'national_id'   => $student->national_id,
            'email'         => $student->email,
            'phone'         => $student->phone,
            'gender'        => $student->gender,
            'date_of_birth' => $student->date_of_birth?->format('Y-m-d'),
            'nationality'   => $student->nationality,
            'avatar'        => $student->avatar ? asset('assets/uploads/' . $student->avatar) : null,
            'class_id'      => $student->class_id,
        ], 'تم تحديث البيانات');
    }

    // GET /my-courses
    public function myCourses(Request $request): JsonResponse
    {
        $student = $request->user();

        $enrollments = Enrollment::with(['course.teacher', 'course.subject'])
            ->where('student_id', $student->id)
            ->where('is_active', true)
            ->latest()
            ->paginate(15);

        $items = collect($enrollments->items())->map(fn ($e) => [
            'enrollment_id'       => $e->id,
            'enrolled_at'         => $e->enrolled_at?->format('Y-m-d'),
            'progress_percentage' => $e->progress_percentage,
            'is_completed'        => $e->is_completed,
            'completed_at'        => $e->completed_at?->format('Y-m-d'),
            'course' => [
                'id'               => $e->course?->id,
                'title'            => $e->course?->title,
                'thumbnail'        => $e->course?->thumbnail ? asset('assets/uploads/courses/' . $e->course->thumbnail) : null,
                'duration_hours'   => $e->course?->duration_hours,
                'difficulty_level' => $e->course?->difficulty_level,
                'teacher'          => $e->course?->teacher?->name,
                'subject'          => $e->course?->subject?->name,
            ],
        ]);

        return response()->json([
            'status'     => true,
            'message'    => 'OK',
            'data'       => $items,
            'pagination' => [
                'current_page' => $enrollments->currentPage(),
                'last_page'    => $enrollments->lastPage(),
                'per_page'     => $enrollments->perPage(),
                'total'        => $enrollments->total(),
            ],
        ]);
    }

    // GET /my-exams
    public function myExams(Request $request): JsonResponse
    {
        $student = $request->user();

        $attempts = $student->examAttempts()
            ->with(['exam.course', 'exam.subject'])
            ->where('status', 'submitted')
            ->latest('submitted_at')
            ->paginate(15);

        $items = collect($attempts->items())->map(fn ($a) => [
            'attempt_id'         => $a->id,
            'score'              => $a->score,
            'total_marks'        => $a->total_marks,
            'percentage'         => (float) $a->percentage,
            'is_passed'          => $a->is_passed,
            'time_taken_minutes' => $a->time_taken_seconds ? (int) ceil($a->time_taken_seconds / 60) : null,
            'submitted_at'       => $a->submitted_at?->format('Y-m-d H:i'),
            'exam' => [
                'id'        => $a->exam?->id,
                'title'     => $a->exam?->title,
                'exam_type' => $a->exam?->exam_type,
                'course'    => $a->exam?->course?->title,
                'subject'   => $a->exam?->subject?->name,
            ],
        ]);

        return response()->json([
            'status'     => true,
            'message'    => 'OK',
            'data'       => $items,
            'pagination' => [
                'current_page' => $attempts->currentPage(),
                'last_page'    => $attempts->lastPage(),
                'per_page'     => $attempts->perPage(),
                'total'        => $attempts->total(),
            ],
        ]);
    }
}
