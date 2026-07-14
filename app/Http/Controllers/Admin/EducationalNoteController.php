<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EducationalNote;
use App\Models\SchoolClass;
use App\Models\Teacher;
use Illuminate\Http\Request;

class EducationalNoteController extends Controller
{
    private function formData(): array
    {
        $teachers = Teacher::orderBy('name')->get();
        $classes  = SchoolClass::where('is_active', true)->orderBy('name')->get();
        return compact('teachers', 'classes');
    }

    public function index()
    {
        $notes = EducationalNote::with(['teacher', 'schoolClass'])
            ->latest()->paginate(20);
        return view('admin.educational_notes.index', compact('notes'));
    }

    public function create()
    {
        extract($this->formData());
        return view('admin.educational_notes.create', compact('teachers', 'classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'teacher_id'  => 'nullable|exists:teachers,id',
            'class_id'    => 'nullable|exists:classes,id',
            'type'        => 'required|in:lesson,homework',
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'attachment'  => 'nullable|file|max:20480',
            'date'        => 'required|date',
        ]);

        $attachment = null;
        if ($request->hasFile('attachment')) {
            $attachment = uploadImage('assets/uploads/educational_notes', $request->file('attachment'));
        }

        EducationalNote::create([
            'teacher_id'  => $request->teacher_id,
            'class_id'    => $request->class_id,
            'type'        => $request->type,
            'title'       => $request->title,
            'description' => $request->description,
            'attachment'  => $attachment,
            'date'        => $request->date,
        ]);

        return redirect()->route('admin.educational-notes.index')
            ->with('success', __('messages.created_successfully'));
    }

    public function edit(EducationalNote $educationalNote)
    {
        extract($this->formData());
        return view('admin.educational_notes.edit', compact('educationalNote', 'teachers', 'classes'));
    }

    public function update(Request $request, EducationalNote $educationalNote)
    {
        $request->validate([
            'teacher_id'  => 'nullable|exists:teachers,id',
            'class_id'    => 'nullable|exists:classes,id',
            'type'        => 'required|in:lesson,homework',
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'attachment'  => 'nullable|file|max:20480',
            'date'        => 'required|date',
        ]);

        $data = [
            'teacher_id'  => $request->teacher_id,
            'class_id'    => $request->class_id,
            'type'        => $request->type,
            'title'       => $request->title,
            'description' => $request->description,
            'date'        => $request->date,
        ];

        if ($request->hasFile('attachment')) {
            $data['attachment'] = uploadImage('assets/uploads/educational_notes', $request->file('attachment'));
        }

        $educationalNote->update($data);

        return redirect()->route('admin.educational-notes.index')
            ->with('success', __('messages.updated_successfully'));
    }

    public function destroy(EducationalNote $educationalNote)
    {
        $educationalNote->delete();
        return redirect()->route('admin.educational-notes.index')
            ->with('success', __('messages.deleted_successfully'));
    }
}
