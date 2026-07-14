<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Category, Course, Subject, Teacher};
use App\Services\CourseService;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function __construct(private CourseService $courses) {}

    public function index(Request $request)
    {
        $courses = $this->courses->list($request->only(['search', 'category_id', 'teacher_id', 'is_published']));
        $categories = Category::active()->get();
        $teachers   = Teacher::where('is_active', true)->get();

        return view('admin.courses.index', compact('courses', 'categories', 'teachers'));
    }

    public function create()
    {
        $categories = Category::roots()->active()->get();
        $subjects   = $this->subjectsWithPath();
        $teachers   = Teacher::where('is_active', true)->get();

        return view('admin.courses.create', compact('categories', 'subjects', 'teachers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'teacher_id'       => 'required|exists:teachers,id',
            'category_id'      => 'nullable|exists:categories,id',
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
            'is_featured'       => 'boolean',
            'is_free'           => 'boolean',
            'sequential_videos' => 'boolean',
            'thumbnail'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data['is_published']      = $request->boolean('is_published');
        $data['is_featured']       = $request->boolean('is_featured');
        $data['is_free']           = $request->boolean('is_free');
        $data['sequential_videos'] = $request->boolean('sequential_videos');

        $this->courses->create($data, $request->file('thumbnail'));

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course created successfully.');
    }

    public function show(int $id)
    {
        $course = $this->courses->find($id);

        return view('admin.courses.show', compact('course'));
    }

    public function edit(int $id)
    {
        $course     = Course::findOrFail($id);
        $categories = Category::roots()->active()->get();
        $subjects   = $this->subjectsWithPath();
        $teachers   = Teacher::where('is_active', true)->get();

        return view('admin.courses.edit', compact('course', 'categories', 'subjects', 'teachers'));
    }

    public function update(Request $request, int $id)
    {
        $course = Course::findOrFail($id);

        $data = $request->validate([
            'teacher_id'       => 'required|exists:teachers,id',
            'category_id'      => 'nullable|exists:categories,id',
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
            'is_featured'       => 'boolean',
            'is_free'           => 'boolean',
            'sequential_videos' => 'boolean',
            'thumbnail'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data['is_published']      = $request->boolean('is_published');
        $data['is_featured']       = $request->boolean('is_featured');
        $data['is_free']           = $request->boolean('is_free');
        $data['sequential_videos'] = $request->boolean('sequential_videos');

        $this->courses->update($course, $data, $request->file('thumbnail'));

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course updated successfully.');
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    private function subjectsWithPath(): \Illuminate\Database\Eloquent\Collection
    {
        return Subject::with(['category.parent.parent.parent'])
            ->active()
            ->get()
            ->sortBy(fn ($s) => $s->full_path);
    }

    public function destroy(int $id)
    {
        $this->courses->delete(Course::findOrFail($id));

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course deleted successfully.');
    }
}
