<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassSubject;
use App\Models\ExternalLink;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;

class ExternalLinkController extends Controller
{
    private function formData(): array
    {
        $classes  = SchoolClass::where('is_active', true)->orderBy('name')->get();
        $teachers = Teacher::orderBy('name')->get();
        $subjects = Subject::active()->get();
        // teacher/class -> subject options for the dependent subject dropdown (JS-filtered client-side).
        $classSubjects = ClassSubject::select('teacher_id', 'class_id', 'subject_id')->get();

        return compact('classes', 'teachers', 'subjects', 'classSubjects');
    }

    public function index(Request $request)
    {
        $links = ExternalLink::with(['schoolClass', 'teacher', 'subject'])
            ->when($request->filled('class_id'), fn ($q) => $q->where('class_id', $request->class_id))
            ->when($request->filled('teacher_id'), fn ($q) => $q->where('teacher_id', $request->teacher_id))
            ->when($request->filled('subject_id'), fn ($q) => $q->where('subject_id', $request->subject_id))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        extract($this->formData());

        return view('admin.external_links.index', compact('links', 'classes', 'teachers', 'subjects'));
    }

    public function create()
    {
        extract($this->formData());
        return view('admin.external_links.create', compact('classes', 'teachers', 'subjects'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'class_id'   => 'required|exists:classes,id',
            'teacher_id' => 'required|exists:teachers,id',
            'subject_id' => 'required|exists:subjects,id',
            'url'        => 'required|url|max:2048',
            'status'     => 'required|boolean',
        ]);

        ExternalLink::create($data);

        return redirect()->route('admin.external-links.index')
            ->with('success', __('messages.created_successfully'));
    }

    public function edit(ExternalLink $externalLink)
    {
        extract($this->formData());
        return view('admin.external_links.edit', compact('externalLink', 'classes', 'teachers', 'subjects'));
    }

    public function update(Request $request, ExternalLink $externalLink)
    {
        $data = $request->validate([
            'class_id'   => 'required|exists:classes,id',
            'teacher_id' => 'required|exists:teachers,id',
            'subject_id' => 'required|exists:subjects,id',
            'url'        => 'required|url|max:2048',
            'status'     => 'required|boolean',
        ]);

        $externalLink->update($data);

        return redirect()->route('admin.external-links.index')
            ->with('success', __('messages.updated_successfully'));
    }

    public function destroy(ExternalLink $externalLink)
    {
        $externalLink->delete();

        return redirect()->route('admin.external-links.index')
            ->with('success', __('messages.deleted_successfully'));
    }
}
