<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\ClassSubject;
use App\Models\Student;
use App\Models\StudentGrade;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'class_id'   => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        $teacher = $request->user();
        $this->authorizeSubject($teacher->id, $request->class_id, $request->subject_id);

        $grades = StudentGrade::where('class_id', $request->class_id)
            ->where('subject_id', $request->subject_id)
            ->when($request->title, fn ($q) => $q->where('title', $request->title))
            ->with('student:id,name')
            ->orderBy('graded_at', 'desc')
            ->get()
            ->map(fn ($g) => [
                'id'         => $g->id,
                'student'    => ['id' => $g->student->id, 'name' => $g->student->name],
                'title'      => $g->title,
                'score'      => (float) $g->score,
                'max_score'  => (float) $g->max_score,
                'percentage' => $g->percentage,
                'graded_at'  => $g->graded_at->format('Y-m-d'),
            ]);

        return $this->success($grades);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'class_id'    => 'required|exists:classes,id',
            'subject_id'  => 'required|exists:subjects,id',
            'title'       => 'required|string|max:200',
            'max_score'   => 'required|numeric|min:1',
            'graded_at'   => 'required|date',
            'grades'      => 'required|array',
            'grades.*.student_id' => 'required|exists:students,id',
            'grades.*.score'      => 'required|numeric|min:0',
        ]);

        $teacher = $request->user();
        $this->authorizeSubject($teacher->id, $data['class_id'], $data['subject_id']);

        foreach ($data['grades'] as $item) {
            StudentGrade::updateOrCreate(
                [
                    'student_id' => $item['student_id'],
                    'class_id'   => $data['class_id'],
                    'subject_id' => $data['subject_id'],
                    'title'      => $data['title'],
                ],
                [
                    'teacher_id' => $teacher->id,
                    'score'      => $item['score'],
                    'max_score'  => $data['max_score'],
                    'graded_at'  => $data['graded_at'],
                ]
            );
        }

        return $this->success(null, 'تم حفظ العلامات بنجاح.');
    }

    private function authorizeSubject(int $teacherId, int $classId, int $subjectId): void
    {
        $ok = ClassSubject::where('class_id', $classId)
            ->where('subject_id', $subjectId)
            ->where('teacher_id', $teacherId)
            ->exists();

        if (! $ok) {
            abort(response()->json(['status' => false, 'message' => 'غير مصرح.'], 403));
        }
    }
}
