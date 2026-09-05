<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\EducationalNote;
use App\Models\Subject;
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

        $query = EducationalNote::with(['teacher', 'schoolClass', 'subject'])
            ->whereDate('date', '<=', now())
            ->orderByDesc('date');

        // If student has a class assigned, filter by it; otherwise return all
        if ($student->class_id) {
            $query->where('class_id', $student->class_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $paginated = $query->paginate(20);

        $items = collect($paginated->items())->map(fn ($note) => $this->noteCard($note));

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

    // GET /educational-notes/dates  [auth]
    // Step 0 of the mobile flow: lists every date that has at least one
    // note (with a subject) for the student's class, newest first, so the
    // app can show a plain tappable list instead of a free date picker.
    public function dates(Request $request): JsonResponse
    {
        $student = $request->user();

        $dates = EducationalNote::query()
            ->whereNotNull('subject_id')
            ->whereDate('date', '<=', now())
            ->when($student->class_id, fn ($q) => $q->where('class_id', $student->class_id))
            ->get(['date', 'type'])
            ->groupBy(fn ($note) => $note->date->format('Y-m-d'))
            ->map(fn ($notes, $date) => [
                'date'           => $date,
                'lessons_count'  => $notes->where('type', 'lesson')->count(),
                'homework_count' => $notes->where('type', 'homework')->count(),
            ])
            ->values()
            ->sortByDesc('date')
            ->values();

        return $this->success($dates);
    }

    // GET /educational-notes/subjects?date=YYYY-MM-DD  [auth]
    // Step 1 of the mobile flow: student taps a date, this returns the
    // subjects that have a lesson/homework note on that date.
    public function subjectsForDate(Request $request): JsonResponse
    {
        $request->validate(['date' => ['required', 'date']]);

        $student = $request->user();

        $notes = EducationalNote::query()
            ->whereDate('date', $request->date)
            ->whereDate('date', '<=', now())
            ->whereNotNull('subject_id')
            ->when($student->class_id, fn ($q) => $q->where('class_id', $student->class_id))
            ->get(['subject_id', 'type']);

        $subjects = Subject::whereIn('id', $notes->pluck('subject_id')->unique())
            ->active()
            ->get()
            ->map(function (Subject $subject) use ($notes) {
                $forSubject = $notes->where('subject_id', $subject->id);

                return [
                    'id'              => $subject->id,
                    'name'            => $subject->name,
                    'icon'            => $subject->icon,
                    'color_class'     => $subject->color_class,
                    'lessons_count'   => $forSubject->where('type', 'lesson')->count(),
                    'homework_count'  => $forSubject->where('type', 'homework')->count(),
                ];
            })
            ->values();

        return $this->success($subjects);
    }

    // GET /educational-notes/content?date=YYYY-MM-DD&subject_id=X  [auth]
    // Step 2 of the mobile flow: student taps a subject from the list
    // returned above, this returns the actual lesson/homework content.
    public function content(Request $request): JsonResponse
    {
        $request->validate([
            'date'       => ['required', 'date'],
            'subject_id' => ['required', 'exists:subjects,id'],
        ]);

        $student = $request->user();

        $notes = EducationalNote::with(['teacher', 'schoolClass', 'subject'])
            ->whereDate('date', $request->date)
            ->whereDate('date', '<=', now())
            ->where('subject_id', $request->subject_id)
            ->when($student->class_id, fn ($q) => $q->where('class_id', $student->class_id))
            ->orderBy('type')
            ->get();

        return $this->success($notes->map(fn ($note) => $this->noteCard($note))->values());
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
            'class'   => $note->schoolClass?->name,
            'subject' => $note->subject ? [
                'id'   => $note->subject->id,
                'name' => $note->subject->name,
            ] : null,
        ];
    }
}
