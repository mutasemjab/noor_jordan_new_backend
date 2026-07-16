<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\ClassSubject;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    use ApiResponse;

    public function myClasses(Request $request): JsonResponse
    {
        $teacher = $request->user();

        $classIds = ClassSubject::where('teacher_id', $teacher->id)
            ->pluck('class_id')
            ->unique();

        $classes = SchoolClass::whereIn('id', $classIds)
            ->withCount('students')
            ->orderBy('name')
            ->get()
            ->map(fn ($c) => [
                'id'             => $c->id,
                'name'           => $c->name,
                'students_count' => $c->students_count,
                'is_homeroom'    => $c->homeroom_teacher_id === $teacher->id,
            ]);

        return $this->success($classes);
    }

    public function students(Request $request, SchoolClass $class): JsonResponse
    {
        $teacher = $request->user();

        // Verify the teacher teaches in this class
        $teaches = ClassSubject::where('class_id', $class->id)
            ->where('teacher_id', $teacher->id)
            ->exists();

        if (! $teaches && $class->homeroom_teacher_id !== $teacher->id) {
            return $this->error('غير مصرح بالوصول لهذا الصف.', 403);
        }

        $students = Student::where('class_id', $class->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'national_id', 'phone', 'avatar', 'gender']);

        return $this->success([
            'class'    => ['id' => $class->id, 'name' => $class->name],
            'students' => $students->map(fn ($s) => [
                'id'          => $s->id,
                'name'        => $s->name,
                'national_id' => $s->national_id,
                'phone'       => $s->phone,
                'gender'      => $s->gender,
                'avatar'      => $s->avatar
                    ? asset('assets/uploads/students/' . $s->avatar)
                    : null,
            ]),
        ]);
    }
}
