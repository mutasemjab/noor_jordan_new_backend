<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\ClassSubject;
use App\Models\ExternalLink;
use App\Models\Teacher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExternalLinkController extends Controller
{
    use ApiResponse;

    // GET /external-links
    public function index(Request $request): JsonResponse
    {
        $teacher = $request->user();

        $paginated = ExternalLink::with(['subject', 'schoolClass'])
            ->where('teacher_id', $teacher->id)
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

    // POST /external-links
    public function store(Request $request): JsonResponse
    {
        $teacher = $request->user();

        $data = $request->validate([
            'subject_id' => ['required', 'exists:subjects,id'],
            'class_id'   => ['required', 'exists:classes,id'],
            'url'        => ['required', 'url', 'max:2048'],
        ]);

        if (! $this->teachesSubjectInClass($teacher, $data['class_id'], $data['subject_id'])) {
            return $this->error('أنت لا تُدرّس هذه المادة لهذا الصف.', 403);
        }

        $item = ExternalLink::create([
            'teacher_id' => $teacher->id,
            'subject_id' => $data['subject_id'],
            'class_id'   => $data['class_id'],
            'url'        => $data['url'],
            'status'     => 1,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'OK',
            'data'    => $this->itemCard($item->load(['subject', 'schoolClass'])),
        ], 201);
    }

    // PUT /external-links/{externalLink}  (mobile sends POST + _method=PUT)
    public function update(Request $request, ExternalLink $externalLink): JsonResponse
    {
        $teacher = $request->user();

        if (! $this->owns($teacher, $externalLink)) {
            return $this->error('غير مصرح بتعديل هذا الرابط.', 403);
        }

        $data = $request->validate([
            'url' => ['required', 'url', 'max:2048'],
        ]);

        $externalLink->update($data);

        return response()->json([
            'status'  => true,
            'message' => 'OK',
            'data'    => $this->itemCard($externalLink->fresh(['subject', 'schoolClass'])),
        ]);
    }

    // DELETE /external-links/{externalLink}
    public function destroy(Request $request, ExternalLink $externalLink): JsonResponse
    {
        $teacher = $request->user();

        if (! $this->owns($teacher, $externalLink)) {
            return $this->error('غير مصرح بحذف هذا الرابط.', 403);
        }

        $externalLink->delete();

        return $this->success(null, 'OK');
    }

    private function teachesSubjectInClass(Teacher $teacher, int $classId, int $subjectId): bool
    {
        return ClassSubject::where('teacher_id', $teacher->id)
            ->where('class_id', $classId)
            ->where('subject_id', $subjectId)
            ->exists();
    }

    private function owns(Teacher $teacher, ExternalLink $item): bool
    {
        return (int) $item->teacher_id === (int) $teacher->id;
    }

    private function itemCard(ExternalLink $item): array
    {
        return [
            'id'      => $item->id,
            'url'     => $item->url,
            'subject' => ['id' => $item->subject?->id, 'name' => $item->subject?->name],
            'class'   => ['id' => $item->schoolClass?->id, 'name' => $item->schoolClass?->name],
        ];
    }
}
