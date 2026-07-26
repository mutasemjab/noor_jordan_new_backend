<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\ClassSubject;
use App\Models\PreviousYearExam;
use App\Models\Teacher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PreviousYearExamController extends Controller
{
    use ApiResponse;

    // GET /previous-year-exams
    public function index(Request $request): JsonResponse
    {
        $teacher = $request->user();

        $paginated = PreviousYearExam::with(['subject', 'schoolClass'])
            ->whereIn('class_id', $this->classIds($teacher))
            ->when($request->filled('subject_id'), fn ($q) => $q->where('subject_id', $request->subject_id))
            ->when($request->filled('class_id'), fn ($q) => $q->where('class_id', $request->class_id))
            ->latest()
            ->paginate(15);

        return response()->json([
            'status'     => true,
            'message'    => 'OK',
            'data'       => collect($paginated->items())->map(fn ($i) => $this->itemCard($i)),
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'last_page'    => $paginated->lastPage(),
                'per_page'     => $paginated->perPage(),
                'total'        => $paginated->total(),
            ],
        ]);
    }

    // POST /previous-year-exams
    public function store(Request $request): JsonResponse
    {
        $teacher = $request->user();

        $data = $request->validate([
            'subject_id' => ['required', 'exists:subjects,id'],
            'class_id'   => ['required', 'exists:classes,id'],
            'title'      => ['required', 'string', 'max:255'],
            'year'       => ['required', 'integer', 'min:1900', 'max:2100'],
            'file'       => ['required', 'mimes:pdf', 'max:20480'],
        ]);

        if (! $this->teachesSubjectInClass($teacher, $data['class_id'], $data['subject_id'])) {
            return $this->error('أنت لا تُدرّس هذه المادة لهذا الصف.', 403);
        }

        $file = $request->file('file');
        $pdf  = uploadImage('assets/uploads/previousYearExam', $file);

        $item = PreviousYearExam::create([
            'teacher_id' => $teacher->id,
            'subject_id' => $data['subject_id'],
            'class_id'   => $data['class_id'],
            'year'       => $data['year'],
            'title_ar'   => $data['title'],
            'title_en'   => null,
            'pdf_file'   => $pdf,
            'file_size'  => round($file->getSize() / 1024 / 1024, 2),
            'sort_order' => 0,
            'status'     => 1,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'OK',
            'data'    => $this->itemCard($item->load(['subject', 'schoolClass'])),
        ], 201);
    }

    // PUT /previous-year-exams/{previousYearExam}  (mobile sends POST + _method=PUT)
    public function update(Request $request, PreviousYearExam $previousYearExam): JsonResponse
    {
        $teacher = $request->user();

        if (! $this->owns($teacher, $previousYearExam)) {
            return $this->error('غير مصرح بتعديل هذا الملف.', 403);
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'year'  => ['required', 'integer', 'min:1900', 'max:2100'],
            'file'  => ['nullable', 'mimes:pdf', 'max:20480'],
        ]);

        $update = ['title_ar' => $data['title'], 'year' => $data['year']];

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $update['pdf_file']  = uploadImage('assets/uploads/previousYearExam', $file);
            $update['file_size'] = round($file->getSize() / 1024 / 1024, 2);
        }

        $previousYearExam->update($update);

        return response()->json([
            'status'  => true,
            'message' => 'OK',
            'data'    => $this->itemCard($previousYearExam->fresh(['subject', 'schoolClass'])),
        ]);
    }

    // DELETE /previous-year-exams/{previousYearExam}
    public function destroy(Request $request, PreviousYearExam $previousYearExam): JsonResponse
    {
        $teacher = $request->user();

        if (! $this->owns($teacher, $previousYearExam)) {
            return $this->error('غير مصرح بحذف هذا الملف.', 403);
        }

        $previousYearExam->delete();

        return $this->success(null, 'OK');
    }

    private function classIds(Teacher $teacher)
    {
        return ClassSubject::where('teacher_id', $teacher->id)->pluck('class_id')->unique();
    }

    private function teachesSubjectInClass(Teacher $teacher, int $classId, int $subjectId): bool
    {
        return ClassSubject::where('teacher_id', $teacher->id)
            ->where('class_id', $classId)
            ->where('subject_id', $subjectId)
            ->exists();
    }

    private function owns(Teacher $teacher, PreviousYearExam $item): bool
    {
        return (int) $item->teacher_id === (int) $teacher->id;
    }

    private function itemCard(PreviousYearExam $item): array
    {
        return [
            'id'        => $item->id,
            'title'     => $item->title,
            'year'      => $item->year,
            'pages'     => $item->pages,
            'file_size' => $item->file_size,
            'pdf_url'   => $item->pdf_file ? asset('assets/uploads/previousYearExam/' . $item->pdf_file) : null,
            'subject'   => ['id' => $item->subject?->id, 'name' => $item->subject?->name],
            'class'     => ['id' => $item->schoolClass?->id, 'name' => $item->schoolClass?->name],
        ];
    }
}
