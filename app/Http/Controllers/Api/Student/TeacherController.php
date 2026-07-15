<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
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
