<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\EducationalNote;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class EducationalNoteController extends Controller
{
    private function teacherId(): int
    {
        return auth('teacher')->id();
    }

    public function index()
    {
        $notes = EducationalNote::with('schoolClass')
            ->where('teacher_id', $this->teacherId())
            ->latest()
            ->paginate(20);
        return view('teacher.educational_notes.index', compact('notes'));
    }

    public function create()
    {
        $classes = SchoolClass::where('is_active', true)->orderBy('name')->get();
        return view('teacher.educational_notes.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
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
            'teacher_id'  => $this->teacherId(),
            'class_id'    => $request->class_id,
            'type'        => $request->type,
            'title'       => $request->title,
            'description' => $request->description,
            'attachment'  => $attachment,
            'date'        => $request->date,
        ]);

        return redirect()->route('teacher.educational-notes.index')
            ->with('success', __('messages.created_successfully'));
    }

    public function edit(EducationalNote $educationalNote)
    {
        abort_unless($educationalNote->teacher_id === $this->teacherId(), 403);
        $classes = SchoolClass::where('is_active', true)->orderBy('name')->get();
        return view('teacher.educational_notes.edit', compact('educationalNote', 'classes'));
    }

    public function update(Request $request, EducationalNote $educationalNote)
    {
        abort_unless($educationalNote->teacher_id === $this->teacherId(), 403);

        $request->validate([
            'class_id'    => 'nullable|exists:classes,id',
            'type'        => 'required|in:lesson,homework',
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'attachment'  => 'nullable|file|max:20480',
            'date'        => 'required|date',
        ]);

        $data = [
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

        return redirect()->route('teacher.educational-notes.index')
            ->with('success', __('messages.updated_successfully'));
    }

    public function destroy(EducationalNote $educationalNote)
    {
        abort_unless($educationalNote->teacher_id === $this->teacherId(), 403);
        $educationalNote->delete();
        return redirect()->route('teacher.educational-notes.index')
            ->with('success', __('messages.deleted_successfully'));
    }
}
