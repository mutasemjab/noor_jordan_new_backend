<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EducationalNoteLog;
use App\Models\SchoolClass;
use App\Models\Teacher;
use Illuminate\Http\Request;

class EducationalNoteLogController extends Controller
{
    public function teachers(Request $request)
    {
        return $this->listFor('teacher', $request);
    }

    public function admins(Request $request)
    {
        return $this->listFor('admin', $request);
    }

    private function listFor(string $actorType, Request $request)
    {
        $logs = EducationalNoteLog::with(['schoolClass', 'teacher'])
            ->where('actor_type', $actorType)
            ->when($request->filled('teacher_id'), fn ($q) => $q->where('teacher_id', $request->teacher_id))
            ->when($request->filled('class_id'), fn ($q) => $q->where('class_id', $request->class_id))
            ->when($request->filled('date'), fn ($q) => $q->whereDate('note_date', $request->date))
            ->latest('created_at')
            ->paginate(30)
            ->withQueryString();

        $teachers = Teacher::orderBy('name')->get();
        $classes  = SchoolClass::orderBy('name')->get();

        return view('admin.educational_note_logs.index', [
            'logs'      => $logs,
            'teachers'  => $teachers,
            'classes'   => $classes,
            'actorType' => $actorType,
        ]);
    }
}
