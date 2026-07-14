<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Category;
use App\Models\Course;
use App\Models\Teacher;
use App\Services\StatsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    use ApiResponse;

    public function __construct(private StatsService $stats) {}

    public function index(Request $request): JsonResponse
    {
        // Root categories
        $categories = Category::active()
            ->roots()
            ->withCount(['children as subcategories_count' => fn ($q) => $q->where('is_active', true)])
            ->get()
            ->map(fn ($cat) => [
                'id'                 => $cat->id,
                'name'               => $cat->name,
                'name_ar'            => $cat->name_ar,
                'name_en'            => $cat->name_en,
                'icon'               => $cat->icon,
                'image'              => $cat->image ? asset('assets/uploads/' . $cat->image) : null,
                'subcategories_count'=> $cat->subcategories_count,
            ]);

        // Featured courses
        $featuredCourses = Course::with(['teacher', 'subject'])
            ->published()
            ->featured()
            ->latest()
            ->take(6)
            ->get()
            ->map(fn ($c) => $this->courseCard($c));

        // Trending courses
        $trendingCourses = Course::with(['teacher', 'subject'])
            ->published()
            ->trending()
            ->latest()
            ->take(6)
            ->get()
            ->map(fn ($c) => $this->courseCard($c));

        // Top teachers
        $teachers = Teacher::where('is_active', true)
            ->orderByDesc('total_students')
            ->take(6)
            ->get()
            ->map(fn ($t) => [
                'id'                  => $t->id,
                'name'                => $t->name,
                'specialization'      => $t->specialization,
                'avatar'              => $t->avatar ? asset('assets/uploads/teachers/' . $t->avatar) : null,
                'average_rating'      => round($t->average_rating ?? 4.8, 1),
                'total_students'      => $t->total_students ?? 0,
                'total_courses'       => $t->total_courses ?? 0,
            ]);

        // Platform stats
        $platformStats = [
            'total_students'  => \App\Models\Student::count(),
            'total_teachers'  => Teacher::where('is_active', true)->count(),
            'total_courses'   => Course::published()->count(),
        ];

        return $this->success([
            'categories'       => $categories,
            'featured_courses' => $featuredCourses,
            'trending_courses' => $trendingCourses,
            'top_teachers'     => $teachers,
            'stats'            => $platformStats,
        ]);
    }

    private function courseCard(Course $course): array
    {
        return [
            'id'               => $course->id,
            'title'            => $course->title,
            'title_ar'         => $course->title_ar,
            'title_en'         => $course->title_en,
            'thumbnail'        => $course->thumbnail ? asset('assets/uploads/courses/' . $course->thumbnail) : null,
            'price'            => $course->price,
            'old_price'        => $course->old_price,
            'is_free'          => $course->is_free,
            'discount'         => $course->discount_percentage,
            'average_rating'   => $course->average_rating,
            'total_students'   => $course->total_students,
            'duration_hours'   => $course->duration_hours,
            'difficulty_level' => $course->difficulty_level,
            'teacher'          => $course->teacher?->name,
            'subject'          => $course->subject?->name,
        ];
    }
}
