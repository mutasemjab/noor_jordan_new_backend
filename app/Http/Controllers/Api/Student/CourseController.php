<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Course;
use App\Services\CourseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    use ApiResponse;

    public function __construct(private CourseService $service) {}

    // GET /courses
    public function index(Request $request): JsonResponse
    {
        $filters = array_merge($request->only(['category_id', 'subject_id', 'teacher_id', 'search']), [
            'is_published' => true,
        ]);

        if ($request->filled('featured')) {
            $filters['is_featured'] = true;
        }
        if ($request->filled('trending')) {
            $filters['is_trending'] = true;
        }

        $paginated = $this->service->list($filters, 15);

        $items = collect($paginated->items())->map(fn ($c) => $this->courseCard($c));

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

    // GET /courses/{id}
    public function show(int $id): JsonResponse
    {
        $course = $this->service->find($id);

        if (! $course->is_published) {
            return $this->error('الدورة غير متاحة', 404);
        }

        return $this->success($this->courseDetail($course));
    }

    private function courseCard(Course $course): array
    {
        return [
            'id'               => $course->id,
            'title'            => $course->title,
            'title_ar'         => $course->title_ar,
            'title_en'         => $course->title_en,
            'description'      => $course->description,
            'thumbnail'        => $course->thumbnail ? asset('assets/uploads/courses/' . $course->thumbnail) : null,
            'price'            => $course->price,
            'old_price'        => $course->old_price,
            'is_free'          => $course->is_free,
            'discount'         => $course->discount_percentage,
            'average_rating'   => $course->average_rating,
            'total_students'   => $course->total_students,
            'duration_hours'   => $course->duration_hours,
            'difficulty_level' => $course->difficulty_level,
            'is_live'          => $course->is_live,
            'teacher' => [
                'id'     => $course->teacher?->id,
                'name'   => $course->teacher?->name,
                'avatar' => $course->teacher?->avatar ? asset('assets/uploads/teachers/' . $course->teacher->avatar) : null,
            ],
            'category' => ['id' => $course->category?->id, 'name' => $course->category?->name],
            'subject'  => ['id' => $course->subject?->id,  'name' => $course->subject?->name],
        ];
    }

    private function courseDetail(Course $course): array
    {
        $data = $this->courseCard($course);

        $data['what_you_learn']  = app()->getLocale() === 'ar' ? $course->what_you_learn_ar : ($course->what_you_learn_en ?? $course->what_you_learn_ar);
        $data['requirements']    = app()->getLocale() === 'ar' ? $course->requirements_ar    : ($course->requirements_en ?? $course->requirements_ar);
        $data['total_videos']    = $course->total_videos;
        $data['total_pdfs']      = $course->total_pdfs;
        $data['sequential']      = $course->sequential_videos;
        $data['enrollments_count'] = $course->enrollments_count ?? 0;

        $data['units'] = $course->units->map(fn ($unit) => [
            'id'          => $unit->id,
            'title'       => $unit->title,
            'order_index' => $unit->order_index,
            'lessons'     => $unit->lessons->where('is_published', true)->map(fn ($l) => [
                'id'               => $l->id,
                'title'            => $l->title,
                'lesson_type'      => $l->lesson_type,
                'duration_minutes' => $l->duration_minutes,
                'order_index'      => $l->order_index,
                'is_free'          => $l->is_free,
            ])->values(),
        ]);

        return $data;
    }
}
