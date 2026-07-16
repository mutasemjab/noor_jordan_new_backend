<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\StudentGrade;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $student = $request->user();

        $grades = StudentGrade::where('student_id', $student->id)
            ->when($request->subject_id, fn ($q) => $q->where('subject_id', $request->subject_id))
            ->with(['subject:id,name_ar,name_en,icon,color_class'])
            ->orderBy('graded_at', 'desc')
            ->get();

        // Group by subject
        $bySubject = $grades->groupBy('subject_id')->map(function ($items) {
            $subject = $items->first()->subject;
            return [
                'subject'    => [
                    'id'          => $subject->id,
                    'name'        => $subject->name_ar,
                    'icon'        => $subject->icon,
                    'color_class' => $subject->color_class,
                ],
                'grades'     => $items->map(fn ($g) => [
                    'id'         => $g->id,
                    'title'      => $g->title,
                    'score'      => (float) $g->score,
                    'max_score'  => (float) $g->max_score,
                    'percentage' => $g->percentage,
                    'graded_at'  => $g->graded_at->format('Y-m-d'),
                ])->values(),
                'average'    => round($items->avg(fn ($g) => $g->percentage), 1),
            ];
        })->values();

        return $this->success($bySubject);
    }
}
