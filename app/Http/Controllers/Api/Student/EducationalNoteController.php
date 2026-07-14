<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\EducationalNote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EducationalNoteController extends Controller
{
    use ApiResponse;

    // GET /educational-notes  [auth]
    // Returns notes for the student's class (class_id)
    public function index(Request $request): JsonResponse
    {
        $student = $request->user();

        $query = EducationalNote::with(['teacher', 'schoolClass'])
            ->orderByDesc('date');

        // If student has a class assigned, filter by it; otherwise return all
        if ($student->class_id) {
            $query->where('class_id', $student->class_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $paginated = $query->paginate(20);

        $items = collect($paginated->items())->map(fn ($note) => [
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
        ]);

        return response()->json([
            'status'     => true,
            'message'    => 'OK',
            'data'       => $items,
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'last_page'    => $paginated->lastPage(),
                'per_page'     => $paginated->perPage(),
                'total'        => $paginated->total(),
            ],
        ]);
    }
}
