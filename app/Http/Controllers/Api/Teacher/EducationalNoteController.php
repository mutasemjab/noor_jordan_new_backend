<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\ClassSubject;
use App\Models\EducationalNote;
use App\Models\SchoolClass;
use App\Models\Teacher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EducationalNoteController extends Controller
{
    use ApiResponse;

    // GET /classes/{class}/educational-notes
    public function index(Request $request, SchoolClass $class): JsonResponse
    {
        $teacher = $request->user();

        if (! $this->teachesClass($teacher, $class)) {
            return $this->error('غير مصرح بالوصول لهذا الصف.', 403);
        }

        $notes = EducationalNote::with(['teacher', 'schoolClass'])
            ->where('class_id', $class->id)
            ->where('teacher_id', $teacher->id)
            ->orderByDesc('date')
            ->paginate(20);

        return response()->json([
            'status'     => true,
            'message'    => 'OK',
            'data'       => collect($notes->items())->map(fn ($n) => $this->noteCard($n)),
            'pagination' => [
                'current_page' => $notes->currentPage(),
                'last_page'    => $notes->lastPage(),
                'per_page'     => $notes->perPage(),
                'total'        => $notes->total(),
            ],
        ]);
    }

    // POST /educational-notes
    public function store(Request $request): JsonResponse
    {
        $teacher = $request->user();

        $data = $request->validate([
            'class_id'    => ['required', 'exists:classes,id'],
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type'        => ['required', 'in:lesson,homework'],
            'date'        => ['required', 'date'],
            'attachment'  => ['nullable', 'file', 'max:20480'],
        ]);

        $class = SchoolClass::findOrFail($data['class_id']);

        if (! $this->teachesClass($teacher, $class)) {
            return $this->error('غير مصرح بإضافة مفكرة لهذا الصف.', 403);
        }

        $attachment = null;
        if ($request->hasFile('attachment')) {
            $attachment = uploadImage('assets/uploads/educational_notes', $request->file('attachment'));
        }

        $note = EducationalNote::create([
            'teacher_id'  => $teacher->id,
            'class_id'    => $data['class_id'],
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'type'        => $data['type'],
            'date'        => $data['date'],
            'attachment'  => $attachment,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'OK',
            'data'    => $this->noteCard($note->load(['teacher', 'schoolClass'])),
        ], 201);
    }

    // PUT /educational-notes/{educationalNote}  (mobile sends POST + _method=PUT)
    public function update(Request $request, EducationalNote $educationalNote): JsonResponse
    {
        $teacher = $request->user();

        if (! $this->ownsNote($teacher, $educationalNote)) {
            return $this->error('غير مصرح بتعديل هذه المفكرة.', 403);
        }

        $data = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type'        => ['required', 'in:lesson,homework'],
            'date'        => ['required', 'date'],
            'attachment'  => ['nullable', 'file', 'max:20480'],
        ]);

        if ($request->hasFile('attachment')) {
            $data['attachment'] = uploadImage('assets/uploads/educational_notes', $request->file('attachment'));
        }

        $educationalNote->update($data);

        return response()->json([
            'status'  => true,
            'message' => 'OK',
            'data'    => $this->noteCard($educationalNote->fresh(['teacher', 'schoolClass'])),
        ]);
    }

    // DELETE /educational-notes/{educationalNote}
    public function destroy(Request $request, EducationalNote $educationalNote): JsonResponse
    {
        $teacher = $request->user();

        if (! $this->ownsNote($teacher, $educationalNote)) {
            return $this->error('غير مصرح بحذف هذه المفكرة.', 403);
        }

        $educationalNote->delete();

        return $this->success(null, 'OK');
    }

    private function teachesClass(Teacher $teacher, SchoolClass $class): bool
    {
        return $class->homeroom_teacher_id === $teacher->id
            || ClassSubject::where('class_id', $class->id)->where('teacher_id', $teacher->id)->exists();
    }

    private function ownsNote(Teacher $teacher, EducationalNote $note): bool
    {
        return (int) $note->teacher_id === (int) $teacher->id;
    }

    private function noteCard(EducationalNote $note): array
    {
        return [
            'id'          => $note->id,
            'title'       => $note->title,
            'description' => $note->description,
            'type'        => $note->type,
            'date'        => $note->date?->format('Y-m-d'),
            'attachment'  => $note->attachment ? asset('assets/uploads/educational_notes/' . $note->attachment) : null,
            'teacher' => [
                'id'     => $note->teacher?->id,
                'name'   => $note->teacher?->name,
                'avatar' => $note->teacher?->avatar ? asset('assets/uploads/teachers/' . $note->teacher->avatar) : null,
            ],
            'class' => $note->schoolClass?->name,
        ];
    }
}
