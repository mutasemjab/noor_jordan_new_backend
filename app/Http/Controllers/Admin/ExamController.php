<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Course, Exam, Question, Subject};
use App\Services\ExamService;
use Illuminate\Http\{JsonResponse, Request};

class ExamController extends Controller
{
    public function __construct(private ExamService $exams) {}

    public function index(Request $request)
    {
        $exams    = $this->exams->list($request->only(['search', 'course_id', 'exam_type', 'is_published']));
        $courses  = Course::where('is_published', true)->get();

        return view('admin.exams.index', compact('exams', 'courses'));
    }

    public function create()
    {
        $courses  = Course::where('is_published', true)->get();
        $subjects = Subject::active()->get();

        return view('admin.exams.create', compact('courses', 'subjects'));
    }

    public function getCourseStructure(int $id): JsonResponse
    {
        $course = Course::with(['units' => fn ($q) => $q->orderBy('order_index'),
                                'units.lessons' => fn ($q) => $q->orderBy('order_index')])
            ->findOrFail($id);

        $units = $course->units->map(fn ($unit) => [
            'id'       => $unit->id,
            'title_ar' => $unit->title_ar,
            'title_en' => $unit->title_en,
            'lessons'  => $unit->lessons->map(fn ($l) => [
                'id'       => $l->id,
                'title_ar' => $l->title_ar,
                'title_en' => $l->title_en,
            ]),
        ]);

        return response()->json(['units' => $units]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'course_id'               => 'nullable|exists:courses,id',
            'unit_id'                 => 'nullable|exists:units,id',
            'lesson_id'               => 'nullable|exists:lessons,id',
            'subject_id'              => 'nullable|exists:subjects,id',
            'title_ar'                => 'required|string|max:255',
            'title_en'                => 'required|string|max:255',
            'description_ar'          => 'nullable|string',
            'description_en'          => 'nullable|string',
            'exam_type'               => 'required|in:mock,unit,final,practice,previous_years,placement',
            'duration_minutes'        => 'required|integer|min:1',
            'total_marks'             => 'required|integer|min:1',
            'pass_marks'              => 'required|integer|min:1',
            'difficulty_level'        => 'nullable|in:easy,medium,hard,mixed',
            'is_published'            => 'boolean',
            'shuffle_questions'       => 'boolean',
            'shuffle_options'         => 'boolean',
            'show_result_immediately' => 'boolean',
            'placement_type'          => 'nullable|in:course,unit,lesson',
        ]);

        $placementType = $request->input('placement_type', 'course');

        // Clear placement fields not relevant to chosen type
        if (empty($data['course_id'])) {
            $data['unit_id']   = null;
            $data['lesson_id'] = null;
        } elseif ($placementType === 'course') {
            $data['unit_id']   = null;
            $data['lesson_id'] = null;
        } elseif ($placementType === 'unit') {
            $data['lesson_id'] = null;
        }
        // if 'lesson': keep both unit_id and lesson_id

        unset($data['placement_type']);

        $data['is_published']            = $request->boolean('is_published');
        $data['shuffle_questions']       = $request->boolean('shuffle_questions');
        $data['shuffle_options']         = $request->boolean('shuffle_options');
        $data['show_result_immediately'] = $request->boolean('show_result_immediately');

        $exam = $this->exams->create($data);

        return redirect()->route('admin.exams.show', $exam->id)
            ->with('success', 'Exam created. Now add questions.');
    }

    public function show(int $id)
    {
        $exam = $this->exams->find($id);

        return view('admin.exams.show', compact('exam'));
    }

    public function edit(int $id)
    {
        $exam     = Exam::findOrFail($id);
        $courses  = Course::where('is_published', true)->get();
        $subjects = Subject::active()->get();

        return view('admin.exams.edit', compact('exam', 'courses', 'subjects'));
    }

    public function update(Request $request, int $id)
    {
        $exam = Exam::findOrFail($id);

        $data = $request->validate([
            'course_id'               => 'nullable|exists:courses,id',
            'unit_id'                 => 'nullable|exists:units,id',
            'lesson_id'               => 'nullable|exists:lessons,id',
            'subject_id'              => 'nullable|exists:subjects,id',
            'title_ar'                => 'required|string|max:255',
            'title_en'                => 'required|string|max:255',
            'description_ar'          => 'nullable|string',
            'description_en'          => 'nullable|string',
            'exam_type'               => 'required|in:mock,unit,final,practice,previous_years,placement',
            'duration_minutes'        => 'required|integer|min:1',
            'total_marks'             => 'required|integer|min:1',
            'pass_marks'              => 'required|integer|min:1',
            'difficulty_level'        => 'nullable|in:easy,medium,hard,mixed',
            'is_published'            => 'boolean',
            'shuffle_questions'       => 'boolean',
            'shuffle_options'         => 'boolean',
            'show_result_immediately' => 'boolean',
            'placement_type'          => 'nullable|in:course,unit,lesson',
        ]);

        $placementType = $request->input('placement_type', 'course');

        if (empty($data['course_id'])) {
            $data['unit_id']   = null;
            $data['lesson_id'] = null;
        } elseif ($placementType === 'course') {
            $data['unit_id']   = null;
            $data['lesson_id'] = null;
        } elseif ($placementType === 'unit') {
            $data['lesson_id'] = null;
        }

        unset($data['placement_type']);

        $data['is_published']            = $request->boolean('is_published');
        $data['shuffle_questions']       = $request->boolean('shuffle_questions');
        $data['shuffle_options']         = $request->boolean('shuffle_options');
        $data['show_result_immediately'] = $request->boolean('show_result_immediately');

        $this->exams->update($exam, $data);

        return redirect()->route('admin.exams.show', $id)
            ->with('success', 'Exam updated successfully.');
    }

    public function destroy(int $id)
    {
        $this->exams->delete(Exam::findOrFail($id));

        return redirect()->route('admin.exams.index')
            ->with('success', 'Exam deleted successfully.');
    }

    // ── Question management ───────────────────────────────────────────

    public function storeQuestion(Request $request, int $examId)
    {
        $exam = Exam::findOrFail($examId);

        $data = $request->validate([
            'question_text_ar'  => 'required|string',
            'question_text_en'  => 'nullable|string',
            'question_image'    => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:5120',
            'question_type'     => 'required|in:mcq,true_false,short_answer,essay',
            'difficulty'        => 'nullable|in:easy,medium,hard',
            'marks'             => 'required|integer|min:1',
            'explanation_ar'    => 'nullable|string',
            'explanation_en'    => 'nullable|string',
            'options'           => 'required_if:question_type,mcq,true_false|array|min:2',
            'options.*.text_ar' => 'required_with:options|string',
            'options.*.text_en' => 'nullable|string',
            'options.*.correct' => 'nullable|boolean',
        ]);

        $imagePath = null;
        if ($request->hasFile('question_image')) {
            $imagePath = uploadImage('assets/uploads/questions', $request->file('question_image'));
        }

        $options = collect($data['options'] ?? [])->map(fn ($o, $i) => [
            'option_text_ar' => $o['text_ar'],
            'option_text_en' => $o['text_en'] ?? null,
            'is_correct'     => isset($o['correct']) && $o['correct'],
            'order_index'    => $i,
        ])->all();

        $this->exams->addQuestion($exam, [
            'question_ar'    => $data['question_text_ar'],
            'question_en'    => $data['question_text_en'] ?? null,
            'image'          => $imagePath,
            'question_type'  => $data['question_type'],
            'difficulty'     => $data['difficulty'] ?? 'medium',
            'marks'          => $data['marks'],
            'explanation_ar' => $data['explanation_ar'] ?? null,
            'explanation_en' => $data['explanation_en'] ?? null,
        ], $options);

        return back()->with('success', 'Question added successfully.');
    }

    public function destroyQuestion(int $questionId)
    {
        $question = Question::with('exam')->findOrFail($questionId);
        $examId   = $question->exam_id;

        $this->exams->deleteQuestion($question);

        return back()->with('success', 'Question deleted.');
    }
}
