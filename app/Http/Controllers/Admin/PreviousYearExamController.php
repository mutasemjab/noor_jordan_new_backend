<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PreviousYearExam;
use App\Models\Subject;
use Illuminate\Http\Request;

class PreviousYearExamController extends Controller
{
    public function index()
    {
        $exams = PreviousYearExam::with('subject')
            ->latest()
            ->paginate(20);

        return view('admin.previous_year_exams.index', compact('exams'));
    }

    public function create()
    {
        $subjects = $this->subjectsWithPath();

        return view('admin.previous_year_exams.create', compact('subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'year' => 'required|integer',

            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',

            'tag_ar' => 'nullable|string|max:255',
            'tag_en' => 'nullable|string|max:255',

            'pages' => 'nullable|integer',
            'file_size' => 'nullable|numeric',

            'sort_order' => 'nullable|integer',

            'status' => 'required|boolean',

            'pdf_file' => 'required|mimes:pdf|max:20480',
        ]);

        $pdfFile = null;

        if ($request->hasFile('pdf_file')) {
            $pdfFile = uploadImage('assets/uploads/previousYearExam', $request->file('pdf_file'));
        }

        PreviousYearExam::create([
            'subject_id' => $request->subject_id,
            'year' => $request->year,

            'title_ar' => $request->title_ar,
            'title_en' => $request->title_en,

            'tag_ar' => $request->tag_ar,
            'tag_en' => $request->tag_en,

            'pages' => $request->pages,
            'file_size' => $request->file_size,

            'pdf_file' => $pdfFile,

            'sort_order' => $request->sort_order ?? 0,
            'status' => $request->status,
        ]);

        return redirect()
            ->route('admin.previous-year-exams.index')
            ->with('success', 'Created Successfully');
    }

    public function edit(PreviousYearExam $previousYearExam)
    {
        $subjects = $this->subjectsWithPath();

        return view('admin.previous_year_exams.edit', compact('previousYearExam', 'subjects'));
    }

    public function update(Request $request, PreviousYearExam $previousYearExam)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'year' => 'required|integer',

            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',

            'tag_ar' => 'nullable|string|max:255',
            'tag_en' => 'nullable|string|max:255',

            'pages' => 'nullable|integer',
            'file_size' => 'nullable|numeric',

            'sort_order' => 'nullable|integer',

            'status' => 'required|boolean',

            'pdf_file' => 'nullable|mimes:pdf|max:20480',
        ]);

        if ($request->hasFile('pdf_file')) {

            $previousYearExam->pdf_file = uploadImage('assets/uploads/previousYearExam', $request->file('pdf_file'));
        }

        $previousYearExam->update([
            'subject_id' => $request->subject_id,
            'year' => $request->year,

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
            ->route('admin.previous-year-exams.index')
            ->with('success', 'Updated Successfully');
    }

    public function destroy(PreviousYearExam $previousYearExam)
    {
        $previousYearExam->delete();

        return redirect()
            ->route('admin.previous-year-exams.index')
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
