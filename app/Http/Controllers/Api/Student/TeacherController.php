<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\ClassSubject;
use App\Models\Teacher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    use ApiResponse;

    // GET /teachers — only the teachers who actually teach the current student's class
    public function index(Request $request): JsonResponse
    {
        $student = $request->user();

        if (! $student->class_id) {
            return $this->success([]);
        }

        $classSubjects = ClassSubject::where('class_id', $student->class_id)
            ->whereNotNull('teacher_id')
            ->with('subject')
            ->get();

        $teachers = Teacher::whereIn('id', $classSubjects->pluck('teacher_id')->unique())
            ->where('is_active', true)
            ->get()
            ->map(function (Teacher $teacher) use ($classSubjects) {
                $subjectNames = $classSubjects
                    ->where('teacher_id', $teacher->id)
                    ->map(fn (ClassSubject $cs) => $cs->subject?->name)
                    ->filter()
                    ->unique()
                    ->values();

                return array_merge($this->teacherCard($teacher), [
                    'subject_name' => $subjectNames->implode('، '),
                ]);
            })
            ->values();

        return $this->success($teachers);
    }

    // GET /teachers/{id}
    public function show(int $id): JsonResponse
    {
        $teacher = Teacher::with(['subjects'])
            ->where('is_active', true)
            ->findOrFail($id);

        $data = $this->teacherCard($teacher);
        $data['subjects'] = $teacher->subjects->map(fn ($s) => [
            'id'   => $s->id,
            'name' => $s->name,
        ]);

        return $this->success($data);
    }

    private function teacherCard(Teacher $teacher): array
    {
        return [
            'id'             => $teacher->id,
            'name'           => $teacher->name,
            'avatar'         => $teacher->avatar ? asset('assets/uploads/teachers/' . $teacher->avatar) : null,
            'total_students' => $teacher->total_students ?? 0,
        ];
    }
}
