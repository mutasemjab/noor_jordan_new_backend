<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassSubject;
use App\Models\ClassSubjectVideo;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class ClassSubjectVideoController extends Controller
{
    public function index(SchoolClass $class)
    {
        $class->load(['classSubjects.subject']);
        $videosBySubject = ClassSubjectVideo::where('class_id', $class->id)
            ->orderBy('subject_id')
            ->orderBy('order_index')
            ->get()
            ->groupBy('subject_id');

        return view('admin.classes.videos', compact('class', 'videosBySubject'));
    }

    public function store(Request $request, SchoolClass $class)
    {
        $data = $request->validate([
            'subject_id'  => 'required|exists:subjects,id',
            'title'       => 'required|string|max:255',
            'youtube_url' => 'required|url',
        ]);

        $max = ClassSubjectVideo::where('class_id', $class->id)
            ->where('subject_id', $data['subject_id'])
            ->max('order_index') ?? 0;

        ClassSubjectVideo::create(array_merge($data, [
            'class_id'    => $class->id,
            'order_index' => $max + 1,
        ]));

        return back()->with('success', 'تم إضافة الفيديو.');
    }

    public function destroy(SchoolClass $class, ClassSubjectVideo $video)
    {
        $video->delete();
        return back()->with('success', 'تم حذف الفيديو.');
    }
}
