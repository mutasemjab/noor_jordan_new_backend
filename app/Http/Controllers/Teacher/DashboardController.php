<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\{Course, Enrollment};
use App\Services\StatsService;

class DashboardController extends Controller
{
    public function __construct(private StatsService $stats) {}

    public function index()
    {
        $teacher = auth()->guard('teacher')->user();
        $stats   = $this->stats->teacherStats($teacher->id);

        $myCourses = Course::where('teacher_id', $teacher->id)
            ->withCount('enrollments')
            ->latest()
            ->limit(5)
            ->get();

        $recentEnrollments = Enrollment::with(['student', 'course'])
            ->whereHas('course', fn ($q) => $q->where('teacher_id', $teacher->id))
            ->latest()
            ->limit(5)
            ->get();


        return view('teacher.dashboard', compact(
            'teacher',
            'stats',
            'myCourses',
            'recentEnrollments'
        ));
    }
}
