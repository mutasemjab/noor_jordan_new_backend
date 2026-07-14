<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PreviousYearExam;
use App\Models\Subject;

class PreviousYearExamController extends Controller
{
    public function index()
    {
        $exams = PreviousYearExam::with('subject')
            ->where('status', 1)
            ->orderByDesc('year')
            ->orderBy('sort_order')
            ->get();

        $years = PreviousYearExam::where('status', 1)
            ->select('year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        $subjects = Subject::whereIn(
            'id',
            $exams->pluck('subject_id')->unique()
        )->get();

        return view(
            'front.previous-years',
            compact('exams', 'years', 'subjects')
        );
    }
}