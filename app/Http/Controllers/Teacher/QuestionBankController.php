<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\QuestionBank;
use App\Models\Subject;
use Illuminate\Http\Request;

class QuestionBankController extends Controller
{
    private function teacherId(): int
    {
        return auth('teacher')->id();
    }

    private function subjectsWithPath(): \Illuminate\Support\Collection
    {
        return auth('teacher')->user()
            ->subjects()
            ->with(['category.parent.parent'])
            ->get()
            ->sortBy(fn($s) => $s->full_path)
            ->values();
    }

    public function index()
    {
        $questionBanks = QuestionBank::with('subject')
            ->where('teacher_id', $this->teacherId())
            ->latest()
            ->paginate(20);
        return view('teacher.question_banks.index', compact('questionBanks'));
    }

    public function create()
    {
        $subjects = $this->subjectsWithPath();
        return view('teacher.question_banks.create', compact('subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject_id' => 'nullable|exists:subjects,id',
            'title_ar'   => 'required|string|max:255',
            'title_en'   => 'nullable|string|max:255',
            'tag_ar'     => 'nullable|string|max:255',
            'tag_en'     => 'nullable|string|max:255',
            'year'       => 'nullable|integer|min:1900|max:2100',
            'pages'      => 'nullable|integer|min:0',
            'file_size'  => 'nullable|numeric',
            'sort_order' => 'nullable|integer',
            'status'     => 'required|boolean',
            'pdf_file'   => 'required|mimes:pdf|max:20480',
        ]);

        $pdf = uploadImage('assets/uploads/questionBank', $request->file('pdf_file'));

        QuestionBank::create([
            'teacher_id' => $this->teacherId(),
            'subject_id' => $request->subject_id,
            'title_ar'   => $request->title_ar,
            'title_en'   => $request->title_en,
            'tag_ar'     => $request->tag_ar,
            'tag_en'     => $request->tag_en,
            'pages'      => $request->pages ?? 0,
            'file_size'  => $request->file_size,
            'sort_order' => $request->sort_order ?? 0,
            'pdf_file'   => $pdf,
            'status'     => $request->status,
        ]);

        return redirect()->route('teacher.question-banks.index')->with('success', __('messages.created_successfully'));
    }

    public function edit(QuestionBank $questionBank)
    {
        abort_unless($questionBank->teacher_id === $this->teacherId(), 403);
        $subjects = $this->subjectsWithPath();
        return view('teacher.question_banks.edit', compact('questionBank', 'subjects'));
    }

    public function update(Request $request, QuestionBank $questionBank)
    {
        abort_unless($questionBank->teacher_id === $this->teacherId(), 403);

        $request->validate([
            'subject_id' => 'nullable|exists:subjects,id',
            'title_ar'   => 'required|string|max:255',
            'title_en'   => 'nullable|string|max:255',
            'tag_ar'     => 'nullable|string|max:255',
            'tag_en'     => 'nullable|string|max:255',
            'pages'      => 'nullable|integer|min:0',
            'file_size'  => 'nullable|numeric',
            'sort_order' => 'nullable|integer',
            'status'     => 'required|boolean',
            'pdf_file'   => 'nullable|mimes:pdf|max:20480',
        ]);

        if ($request->hasFile('pdf_file')) {
            $questionBank->pdf_file = uploadImage('assets/uploads/questionBank', $request->file('pdf_file'));
        }

        $questionBank->update([
            'subject_id' => $request->subject_id,
            'title_ar'   => $request->title_ar,
            'title_en'   => $request->title_en,
            'tag_ar'     => $request->tag_ar,
            'tag_en'     => $request->tag_en,
            'pages'      => $request->pages ?? 0,
            'file_size'  => $request->file_size,
            'sort_order' => $request->sort_order ?? 0,
            'status'     => $request->status,
        ]);

        return redirect()->route('teacher.question-banks.index')->with('success', __('messages.updated_successfully'));
    }

    public function destroy(QuestionBank $questionBank)
    {
        abort_unless($questionBank->teacher_id === $this->teacherId(), 403);
        $questionBank->delete();
        return redirect()->route('teacher.question-banks.index')->with('success', __('messages.deleted_successfully'));
    }
}
