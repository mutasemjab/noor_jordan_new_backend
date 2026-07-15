<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Services\StatsService;

class DashboardController extends Controller
{
    public function __construct(private StatsService $stats) {}

    public function index()
    {
        $teacher = auth()->guard('teacher')->user();
        $stats   = $this->stats->teacherStats($teacher->id);

        return view('teacher.dashboard', compact('teacher', 'stats'));
    }
}
