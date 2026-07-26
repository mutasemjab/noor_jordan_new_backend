<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\ClassSubject;
use App\Models\QuestionBank;
use App\Models\Teacher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuestionBankController extends Controller
{
    use ApiResponse;

    // GET /question-banks — all question banks for the teacher's own classes
    public function index(Request $request): JsonResponse
    {
        $teacher = $request->user();

        $paginated = QuestionBank::with(['subject', 'schoolClass'])
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

    // POST /question-banks
    public function store(Request $request): JsonResponse
    {
        $teacher = $request->user();

        $data = $request->validate([
            'subject_id' => ['required', 'exists:subjects,id'],
            'class_id'   => ['required', 'exists:classes,id'],
            'title'      => ['required', 'string', 'max:255'],
            'file'       => ['required', 'mimes:pdf', 'max:20480'],
        ]);

        if (! $this->teachesSubjectInClass($teacher, $data['class_id'], $data['subject_id'])) {
            return $this->error('أنت لا تُدرّس هذه المادة لهذا الصف.', 403);
        }

        $file = $request->file('file');
        $pdf  = uploadImage('assets/uploads/questionBank', $file);

        $item = QuestionBank::create([
            'teacher_id' => $teacher->id,
            'subject_id' => $data['subject_id'],
            'class_id'   => $data['class_id'],
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

    // PUT /question-banks/{questionBank}  (mobile sends POST + _method=PUT)
    public function update(Request $request, QuestionBank $questionBank): JsonResponse
    {
        $teacher = $request->user();

        if (! $this->owns($teacher, $questionBank)) {
            return $this->error('غير مصرح بتعديل هذا الملف.', 403);
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'file'  => ['nullable', 'mimes:pdf', 'max:20480'],
        ]);

        $update = ['title_ar' => $data['title']];

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $update['pdf_file']  = uploadImage('assets/uploads/questionBank', $file);
            $update['file_size'] = round($file->getSize() / 1024 / 1024, 2);
        }

        $questionBank->update($update);

        return response()->json([
            'status'  => true,
            'message' => 'OK',
            'data'    => $this->itemCard($questionBank->fresh(['subject', 'schoolClass'])),
        ]);
    }

    // DELETE /question-banks/{questionBank}
    public function destroy(Request $request, QuestionBank $questionBank): JsonResponse
    {
        $teacher = $request->user();

        if (! $this->owns($teacher, $questionBank)) {
            return $this->error('غير مصرح بحذف هذا الملف.', 403);
        }

        $questionBank->delete();

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

    private function owns(Teacher $teacher, QuestionBank $item): bool
    {
        return (int) $item->teacher_id === (int) $teacher->id;
    }

    private function itemCard(QuestionBank $item): array
    {
        return [
            'id'        => $item->id,
            'title'     => $item->title,
            'pages'     => $item->pages,
            'file_size' => $item->file_size,
            'pdf_url'   => $item->pdf_file ? asset('assets/uploads/questionBank/' . $item->pdf_file) : null,
            'subject'   => ['id' => $item->subject?->id, 'name' => $item->subject?->name],
            'class'     => ['id' => $item->schoolClass?->id, 'name' => $item->schoolClass?->name],
        ];
    }
}
