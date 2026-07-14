<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function index(Request $request)
    {
        $enrollments = Enrollment::with(['student', 'course'])
            ->when($request->search, fn ($q, $s) => $q->whereHas('student', fn ($sq) => $sq
                ->where('name', 'like', "%{$s}%")
                ->orWhere('email', 'like', "%{$s}%")
            ))
            ->when($request->course_id, fn ($q, $c) => $q->where('course_id', $c))
            ->when($request->is_active !== null && $request->is_active !== '',
                fn ($q) => $q->where('is_active', $request->boolean('is_active'))
            )
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $courses = Course::select('id', 'title_ar', 'title_en')->orderBy('title_en')->get();

        return view('admin.enrollments.index', compact('enrollments', 'courses'));
    }

    public function toggleActive(Enrollment $enrollment)
    {
        $enrollment->update(['is_active' => !$enrollment->is_active]);

        return back()->with('success', __('messages.enrollment_status_updated'));
    }

    public function destroy(Enrollment $enrollment)
    {
        $enrollment->delete();

        return back()->with('success', __('messages.enrollment_deleted'));
    }
}
