<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\{Category, Course, Subject};
use App\Services\CourseService;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function __construct(private CourseService $courses) {}

    private function teacher()
    {
        return auth()->guard('teacher')->user();
    }

    public function index(Request $request)
    {
        $filters             = $request->only(['search', 'category_id', 'is_published']);
        $filters['teacher_id'] = $this->teacher()->id;

        $courses    = $this->courses->list($filters);
        $categories = $this->teacherCategories();

        return view('teacher.courses.index', compact('courses', 'categories'));
    }

    private function teacherSubjects(): \Illuminate\Support\Collection
    {
        return $this->teacher()
            ->subjects()
            ->with(['category.parent.parent'])
            ->get()
            ->sortBy(fn($s) => $s->full_path)
            ->values();
    }

    private function teacherCategories(): \Illuminate\Support\Collection
    {
        return $this->teacher()
            ->subjects()
            ->with(['category.parent.parent'])
            ->get()
            ->pluck('category')
            ->filter()
            ->unique('id')
            ->sortBy(fn($c) => $c->full_path)
            ->values();
    }

    public function create()
    {
        $categories = $this->teacherCategories();
        $subjects   = $this->teacherSubjects();

        return view('teacher.courses.create', compact('categories', 'subjects'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id'      => 'required|exists:categories,id',
            'subject_id'       => 'nullable|exists:subjects,id',
            'title_ar'         => 'required|string|max:255',
            'title_en'         => 'required|string|max:255',
            'description_ar'   => 'nullable|string',
            'description_en'   => 'nullable|string',
            'what_you_learn_ar'=> 'nullable|string',
            'what_you_learn_en'=> 'nullable|string',
            'requirements_ar'  => 'nullable|string',
            'requirements_en'  => 'nullable|string',
            'price'            => 'required|numeric|min:0',
            'old_price'        => 'nullable|numeric|min:0',
            'duration_hours'   => 'nullable|integer|min:0',
            'difficulty_level' => 'nullable|in:beginner,intermediate,advanced',
            'is_published'      => 'boolean',
            'is_free'           => 'boolean',
            'sequential_videos' => 'boolean',
            'thumbnail'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data['teacher_id']         = $this->teacher()->id;
        $data['is_published']       = $request->boolean('is_published');
        $data['is_free']            = $request->boolean('is_free');
        $data['sequential_videos']  = $request->boolean('sequential_videos');

        $course = $this->courses->create($data, $request->file('thumbnail'));

        return redirect()->route('teacher.courses.show', $course->id)
            ->with('success', 'Course created. Now add units and lessons.');
    }

    public function show(int $id)
    {
        $course = $this->courses->find($id);

        abort_if($course->teacher_id !== $this->teacher()->id, 403);

        return view('teacher.courses.show', compact('course'));
    }

    public function edit(int $id)
    {
        $course = Course::findOrFail($id);

        abort_if($course->teacher_id !== $this->teacher()->id, 403);

        $categories = $this->teacherCategories();
        $subjects   = $this->teacherSubjects();

        return view('teacher.courses.edit', compact('course', 'categories', 'subjects'));
    }

    public function update(Request $request, int $id)
    {
        $course = Course::findOrFail($id);

        abort_if($course->teacher_id !== $this->teacher()->id, 403);

        $data = $request->validate([
            'category_id'      => 'required|exists:categories,id',
            'subject_id'       => 'nullable|exists:subjects,id',
            'title_ar'         => 'required|string|max:255',
            'title_en'         => 'required|string|max:255',
            'description_ar'   => 'nullable|string',
            'description_en'   => 'nullable|string',
            'what_you_learn_ar'=> 'nullable|string',
            'what_you_learn_en'=> 'nullable|string',
            'requirements_ar'  => 'nullable|string',
            'requirements_en'  => 'nullable|string',
            'price'            => 'required|numeric|min:0',
            'old_price'        => 'nullable|numeric|min:0',
            'duration_hours'   => 'nullable|integer|min:0',
            'difficulty_level' => 'nullable|in:beginner,intermediate,advanced',
            'is_published'      => 'boolean',
            'is_free'           => 'boolean',
            'sequential_videos' => 'boolean',
            'thumbnail'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data['is_published']      = $request->boolean('is_published');
        $data['is_free']           = $request->boolean('is_free');
        $data['sequential_videos'] = $request->boolean('sequential_videos');

        $this->courses->update($course, $data, $request->file('thumbnail'));

        return redirect()->route('teacher.courses.show', $id)
            ->with('success', 'Course updated successfully.');
    }

    public function destroy(int $id)
    {
        $course = Course::findOrFail($id);

        abort_if($course->teacher_id !== $this->teacher()->id, 403);

        $this->courses->delete($course);

        return redirect()->route('teacher.courses.index')
            ->with('success', 'Course deleted.');
    }
}
