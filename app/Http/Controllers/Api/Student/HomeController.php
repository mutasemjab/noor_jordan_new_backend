<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\JsonResponse;

class HomeController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $teachers = Teacher::where('is_active', true)
            ->orderByDesc('total_students')
            ->take(6)
            ->get()
            ->map(fn ($t) => [
                'id'             => $t->id,
                'name'           => $t->name,
                'avatar'         => $t->avatar ? asset('assets/uploads/teachers/' . $t->avatar) : null,
                'total_students' => $t->total_students ?? 0,
            ]);

        $platformStats = [
            'total_students' => Student::count(),
            'total_teachers' => Teacher::where('is_active', true)->count(),
        ];

        return $this->success([
            'top_teachers' => $teachers,
            'stats'        => $platformStats,
        ]);
    }
}
