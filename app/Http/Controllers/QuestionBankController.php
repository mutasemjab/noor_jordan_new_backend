<?php

namespace App\Http\Controllers;

use App\Models\QuestionBank;
use App\Models\Subject;
use Illuminate\Http\Request;

class QuestionBankController extends Controller
{
     public function index()
    {
        $exams = QuestionBank::with('subject')
            ->where('status', 1)
            ->orderBy('sort_order')
            ->get();

        $subjects = Subject::whereIn(
            'id',
            $exams->pluck('subject_id')->unique()
        )->get();

        return view(
            'front.question-bank',
            compact('exams', 'subjects')
        );
    }
}