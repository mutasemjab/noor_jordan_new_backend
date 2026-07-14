<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Course;
use App\Models\Teacher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    use ApiResponse;

    // GET /teachers
    public function index(Request $request): JsonResponse
    {
        $query = Teacher::where('is_active', true)
            ->orderByDesc('total_students');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn ($q) => $q
                ->where('name', 'like', "%{$s}%")
                ->orWhere('specialization_ar', 'like', "%{$s}%")
                ->orWhere('specialization_en', 'like', "%{$s}%")
            );
        }

        $paginated = $query->paginate(15);

        return response()->json([
            'status'     => true,
            'message'    => 'OK',
            'data'       => collect($paginated->items())->map(fn ($t) => $this->teacherCard($t)),
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'last_page'    => $paginated->lastPage(),
                'per_page'     => $paginated->perPage(),
                'total'        => $paginated->total(),
            ],
        ]);
    }

    // GET /teachers/{id}
    public function show(int $id): JsonResponse
    {
        $teacher = Teacher::where('is_active', true)->findOrFail($id);

        $courses = Course::with(['subject', 'category'])
            ->published()
            ->where('teacher_id', $id)
            ->latest()
            ->get()
            ->map(fn ($c) => [
                'id'               => $c->id,
                'title'            => $c->title,
                'thumbnail'        => $c->thumbnail ? asset('assets/uploads/courses/' . $c->thumbnail) : null,
                'price'            => $c->price,
                'is_free'          => $c->is_free,
                'average_rating'   => $c->average_rating,
                'total_students'   => $c->total_students,
                'duration_hours'   => $c->duration_hours,
                'difficulty_level' => $c->difficulty_level,
                'subject'          => $c->subject?->name,
            ]);

        $data = $this->teacherCard($teacher);
        $data['bio']           = $teacher->bio;
        $data['qualification'] = $teacher->qualification;
        $data['courses']       = $courses;

        return $this->success($data);
    }

    private function teacherCard(Teacher $teacher): array
    {
        return [
            'id'                  => $teacher->id,
            'name'                => $teacher->name,
            'specialization'      => $teacher->specialization,
            'avatar'              => $teacher->avatar ? asset('assets/uploads/teachers/' . $teacher->avatar) : null,
            'years_of_experience' => $teacher->years_of_experience,
            'average_rating'      => round($teacher->average_rating ?? 4.8, 1),
            'total_students'      => $teacher->total_students ?? 0,
            'total_courses'       => $teacher->total_courses ?? 0,
            'is_verified'         => $teacher->is_verified,
        ];
    }
}
