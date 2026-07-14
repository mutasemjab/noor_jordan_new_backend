<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{ContactMessage, Course, Enrollment};
use App\Services\StatsService;

class DashboardController extends Controller
{
    public function __construct(private StatsService $stats) {}

    public function index()
    {
        $stats = $this->stats->adminStats();

        $recentEnrollments = Enrollment::with(['student', 'course.teacher'])
            ->latest()
            ->limit(5)
            ->get();

        $topCourses = Course::withCount('enrollments')
            ->where('is_published', true)
            ->orderByDesc('enrollments_count')
            ->limit(5)
            ->get();

        $recentContacts = ContactMessage::where('status', 'new')
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'recentEnrollments',
            'topCourses',
            'recentContacts',
        ));
    }
}
