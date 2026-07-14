<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Category;
use App\Models\Course;
use App\Models\Subject;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    use ApiResponse;

    // GET /categories — root categories
    public function index(): JsonResponse
    {
        $categories = Category::active()
            ->roots()
            ->withCount(['children as subcategories_count' => fn ($q) => $q->where('is_active', true)])
            ->get()
            ->map(fn ($cat) => $this->catData($cat, true));

        return $this->success($categories);
    }

    // GET /categories/{id} — category with its children
    public function show(int $id): JsonResponse
    {
        $category = Category::with([
            'children' => fn ($q) => $q->where('is_active', true)->orderBy('order_index'),
        ])->where('is_active', true)->findOrFail($id);

        // If this category has subjects (leaf level), include them
        $subjects = $category->subjects()
            ->where('is_active', true)
            ->orderBy('order_index')
            ->get()
            ->map(fn ($s) => $this->subjectData($s));

        return $this->success([
            'category'    => $this->catData($category),
            'children'    => $category->children->map(fn ($c) => $this->catData($c, true)),
            'subjects'    => $subjects,
        ]);
    }

    // GET /subjects/{id} — subject detail with courses
    public function subject(int $id): JsonResponse
    {
        $subject = Subject::with(['category'])
            ->where('is_active', true)
            ->findOrFail($id);

        $courses = Course::with(['teacher'])
            ->published()
            ->where('subject_id', $id)
            ->latest()
            ->get()
            ->map(fn ($c) => [
                'id'               => $c->id,
                'title'            => $c->title,
                'title_ar'         => $c->title_ar,
                'title_en'         => $c->title_en,
                'description'      => $c->description,
                'thumbnail'        => $c->thumbnail ? asset('assets/uploads/' . $c->thumbnail) : null,
                'price'            => $c->price,
                'old_price'        => $c->old_price,
                'is_free'          => $c->is_free,
                'discount'         => $c->discount_percentage,
                'average_rating'   => $c->average_rating,
                'total_students'   => $c->total_students,
                'duration_hours'   => $c->duration_hours,
                'difficulty_level' => $c->difficulty_level,
                'teacher'          => [
                    'id'     => $c->teacher?->id,
                    'name'   => $c->teacher?->name,
                    'avatar' => $c->teacher?->avatar ? asset('assets/uploads/' . $c->teacher->avatar) : null,
                ],
            ]);

        return $this->success([
            'subject' => $this->subjectData($subject),
            'courses' => $courses,
        ]);
    }

    private function catData(Category $cat, bool $withCount = false): array
    {
        $data = [
            'id'      => $cat->id,
            'name'    => $cat->name,
            'name_ar' => $cat->name_ar,
            'name_en' => $cat->name_en,
            'level'   => $cat->level,
            'icon'    => $cat->icon,
            'image'   => $cat->image ? asset('assets/uploads/' . $cat->image) : null,
            'has_children' => $cat->children_count ?? ($cat->relationLoaded('children') ? $cat->children->count() : null),
        ];

        if ($withCount && isset($cat->subcategories_count)) {
            $data['subcategories_count'] = $cat->subcategories_count;
        }

        return $data;
    }

    private function subjectData(Subject $subject): array
    {
        return [
            'id'          => $subject->id,
            'name'        => $subject->name,
            'name_ar'     => $subject->name_ar,
            'name_en'     => $subject->name_en,
            'icon'        => $subject->icon,
            'color_class' => $subject->color_class,
            'is_elective' => $subject->is_elective,
            'category_id' => $subject->category_id,
        ];
    }
}
