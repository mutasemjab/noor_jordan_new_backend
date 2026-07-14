<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuestionBank;
use App\Models\Subject;
use Illuminate\Http\Request;

class QuestionBankController extends Controller
{
    public function index()
    {
        $questionBanks = QuestionBank::with('subject')
            ->latest()
            ->paginate(20);

        return view('admin.question_banks.index',
            compact('questionBanks')
        );
    }

    public function create()
    {
        $subjects = $this->subjectsWithPath();

        return view('admin.question_banks.create', compact('subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',

            'title_ar' => 'required',
            'title_en' => 'required',

            'tag_ar' => 'nullable',
            'tag_en' => 'nullable',

            'pages' => 'nullable|integer',
            'file_size' => 'nullable|numeric',

            'sort_order' => 'nullable|integer',

            'status' => 'required|boolean',

            'pdf_file' => 'required|mimes:pdf|max:20480',
        ]);

        $pdf = uploadImage('assets/uploads/questionBank', $request->file('pdf_file'));

        QuestionBank::create([
            'subject_id' => $request->subject_id,

            'title_ar' => $request->title_ar,
            'title_en' => $request->title_en,

            'tag_ar' => $request->tag_ar,
            'tag_en' => $request->tag_en,

            'pdf_file' => $pdf,

            'pages' => $request->pages,
            'file_size' => $request->file_size,

            'sort_order' => $request->sort_order ?? 0,

            'status' => $request->status,
        ]);

        return redirect()
            ->route('admin.question-banks.index')
            ->with('success', 'Created Successfully');
    }

    public function edit(QuestionBank $questionBank)
    {
        $subjects = $this->subjectsWithPath();

        return view('admin.question_banks.edit', compact('questionBank', 'subjects'));
    }

    public function update(Request $request, QuestionBank $questionBank)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',

            'title_ar' => 'required',
            'title_en' => 'required',

            'tag_ar' => 'nullable',
            'tag_en' => 'nullable',

            'pages' => 'nullable|integer',
            'file_size' => 'nullable|numeric',

            'sort_order' => 'nullable|integer',

            'status' => 'required|boolean',

            'pdf_file' => 'nullable|mimes:pdf|max:20480',
        ]);

        if ($request->hasFile('pdf_file')) {


            $questionBank->pdf_file = uploadImage('assets/uploads/questionBank', $request->file('pdf_file'));
        }

        $questionBank->update([
            'subject_id' => $request->subject_id,

            'title_ar' => $request->title_ar,
            'title_en' => $request->title_en,

            'tag_ar' => $request->tag_ar,
            'tag_en' => $request->tag_en,

            'pages' => $request->pages,
            'file_size' => $request->file_size,

            'sort_order' => $request->sort_order ?? 0,

            'status' => $request->status,
        ]);

        return redirect()
            ->route('admin.question-banks.index')
            ->with('success', 'Updated Successfully');
    }

    public function destroy(QuestionBank $questionBank)
    {
        $questionBank->delete();

        return redirect()
            ->route('admin.question-banks.index')
            ->with('success', 'Deleted Successfully');
    }

    private function subjectsWithPath(): \Illuminate\Support\Collection
    {
        return Subject::with(['category.parent.parent'])
            ->get()
            ->sortBy(fn ($s) => $s->full_path)
            ->values();
    }
}
