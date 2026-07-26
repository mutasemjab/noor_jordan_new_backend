<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\ClassSubject;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Teacher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExamController extends Controller
{
    use ApiResponse;

    private const QUESTION_TYPES = ['mcq', 'true_false'];

    // GET /exams — exams for the teacher's own classes
    public function index(Request $request): JsonResponse
    {
        $teacher = $request->user();

        $paginated = Exam::with(['subject', 'schoolClass'])
            ->withCount('questions')
            ->whereIn('class_id', $this->classIds($teacher))
            ->when($request->filled('subject_id'), fn ($q) => $q->where('subject_id', $request->subject_id))
            ->when($request->filled('class_id'), fn ($q) => $q->where('class_id', $request->class_id))
            ->latest()
            ->paginate(15);

        return response()->json([
            'status'     => true,
            'message'    => 'OK',
            'data'       => collect($paginated->items())->map(fn ($e) => $this->examCard($e)),
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'last_page'    => $paginated->lastPage(),
                'per_page'     => $paginated->perPage(),
                'total'        => $paginated->total(),
            ],
        ]);
    }

    // GET /exams/{exam} — full exam with questions/options (for editing)
    public function show(Request $request, Exam $exam): JsonResponse
    {
        $teacher = $request->user();

        if (! $this->owns($teacher, $exam)) {
            return $this->error('غير مصرح بالوصول لهذا الامتحان.', 403);
        }

        $exam->load(['subject', 'schoolClass', 'questions.options']);

        $data = $this->examCard($exam);
        $data['questions'] = $exam->questions->map(fn ($q) => $this->questionCard($q))->values();

        return $this->success($data);
    }

    // POST /exams — create exam, optionally with a full set of questions in one shot
    public function store(Request $request): JsonResponse
    {
        $teacher = $request->user();

        $data = $request->validate([
            'class_id'                => ['required', 'exists:classes,id'],
            'subject_id'              => ['required', 'exists:subjects,id'],
            'title_ar'                => ['required', 'string', 'max:255'],
            'title_en'                => ['nullable', 'string', 'max:255'],
            'description_ar'          => ['nullable', 'string'],
            'description_en'          => ['nullable', 'string'],
            'exam_type'               => ['required', 'in:mock,unit,final,practice,previous_years,placement'],
            'duration_minutes'        => ['required', 'integer', 'min:1'],
            'total_marks'             => ['required', 'integer', 'min:1'],
            'pass_marks'              => ['required', 'integer', 'min:1'],
            'difficulty_level'        => ['nullable', 'in:easy,medium,hard,mixed'],
            'is_published'            => ['boolean'],
            'show_result_immediately' => ['boolean'],

            'questions'                             => ['nullable', 'array'],
            'questions.*.question_ar'               => ['required_with:questions', 'string'],
            'questions.*.question_en'                => ['nullable', 'string'],
            'questions.*.question_type'              => ['required_with:questions', 'in:' . implode(',', self::QUESTION_TYPES)],
            'questions.*.marks'                      => ['required_with:questions', 'integer', 'min:1'],
            'questions.*.difficulty'                 => ['nullable', 'in:easy,medium,hard'],
            'questions.*.explanation_ar'              => ['nullable', 'string'],
            'questions.*.explanation_en'              => ['nullable', 'string'],
            'questions.*.options'                    => ['required_with:questions', 'array', 'min:2'],
            'questions.*.options.*.option_text_ar'   => ['required_with:questions.*.options', 'string'],
            'questions.*.options.*.option_text_en'   => ['nullable', 'string'],
            'questions.*.options.*.is_correct'       => ['boolean'],
        ]);

        if (! $this->teachesSubjectInClass($teacher, $data['class_id'], $data['subject_id'])) {
            return $this->error('أنت لا تُدرّس هذه المادة لهذا الصف.', 403);
        }

        if ($error = $this->validateQuestionsShape($data['questions'] ?? [])) {
            return $this->error($error, 422);
        }

        $exam = DB::transaction(function () use ($data, $teacher) {
            $exam = Exam::create([
                'class_id'                => $data['class_id'],
                'subject_id'              => $data['subject_id'],
                'teacher_id'              => $teacher->id,
                'title_ar'                => $data['title_ar'],
                'title_en'                => $data['title_en'] ?? null,
                'description_ar'          => $data['description_ar'] ?? null,
                'description_en'          => $data['description_en'] ?? null,
                'exam_type'               => $data['exam_type'],
                'duration_minutes'        => $data['duration_minutes'],
                'total_marks'             => $data['total_marks'],
                'pass_marks'              => $data['pass_marks'],
                'difficulty_level'        => $data['difficulty_level'] ?? 'medium',
                'is_published'            => $data['is_published'] ?? false,
                'show_result_immediately' => $data['show_result_immediately'] ?? true,
                'total_questions'         => count($data['questions'] ?? []),
            ]);

            foreach ($data['questions'] ?? [] as $i => $q) {
                $this->createQuestion($exam, $q, $i);
            }

            return $exam;
        });

        $exam->load(['subject', 'schoolClass', 'questions.options']);
        $result = $this->examCard($exam);
        $result['questions'] = $exam->questions->map(fn ($q) => $this->questionCard($q))->values();

        return response()->json(['status' => true, 'message' => 'OK', 'data' => $result], 201);
    }

    // PUT /exams/{exam} — metadata only (use the questions endpoints below to edit questions)
    public function update(Request $request, Exam $exam): JsonResponse
    {
        $teacher = $request->user();

        if (! $this->owns($teacher, $exam)) {
            return $this->error('غير مصرح بتعديل هذا الامتحان.', 403);
        }

        $data = $request->validate([
            'title_ar'                => ['required', 'string', 'max:255'],
            'title_en'                => ['nullable', 'string', 'max:255'],
            'description_ar'          => ['nullable', 'string'],
            'description_en'          => ['nullable', 'string'],
            'exam_type'               => ['required', 'in:mock,unit,final,practice,previous_years,placement'],
            'duration_minutes'        => ['required', 'integer', 'min:1'],
            'total_marks'             => ['required', 'integer', 'min:1'],
            'pass_marks'              => ['required', 'integer', 'min:1'],
            'difficulty_level'        => ['nullable', 'in:easy,medium,hard,mixed'],
            'is_published'            => ['boolean'],
            'show_result_immediately' => ['boolean'],
        ]);

        $exam->update($data);

        return $this->success($this->examCard($exam->fresh(['subject', 'schoolClass'])));
    }

    // DELETE /exams/{exam}
    public function destroy(Request $request, Exam $exam): JsonResponse
    {
        $teacher = $request->user();

        if (! $this->owns($teacher, $exam)) {
            return $this->error('غير مصرح بحذف هذا الامتحان.', 403);
        }

        $exam->delete();

        return $this->success(null, 'OK');
    }

    // POST /exams/{exam}/questions — add a single question after creation
    public function storeQuestion(Request $request, Exam $exam): JsonResponse
    {
        $teacher = $request->user();

        if (! $this->owns($teacher, $exam)) {
            return $this->error('غير مصرح.', 403);
        }

        $data = $request->validate([
            'question_ar'             => ['required', 'string'],
            'question_en'             => ['nullable', 'string'],
            'question_type'           => ['required', 'in:' . implode(',', self::QUESTION_TYPES)],
            'marks'                   => ['required', 'integer', 'min:1'],
            'difficulty'              => ['nullable', 'in:easy,medium,hard'],
            'explanation_ar'          => ['nullable', 'string'],
            'explanation_en'          => ['nullable', 'string'],
            'options'                 => ['required', 'array', 'min:2'],
            'options.*.option_text_ar' => ['required', 'string'],
            'options.*.option_text_en' => ['nullable', 'string'],
            'options.*.is_correct'    => ['boolean'],
        ]);

        if ($error = $this->validateQuestionsShape([$data])) {
            return $this->error($error, 422);
        }

        $question = DB::transaction(function () use ($exam, $data) {
            $maxOrder = $exam->questions()->max('order_index');
            $question = $this->createQuestion($exam, $data, ($maxOrder === null ? 0 : $maxOrder + 1));
            $exam->increment('total_questions');

            return $question;
        });

        return response()->json([
            'status'  => true,
            'message' => 'OK',
            'data'    => $this->questionCard($question->load('options')),
        ], 201);
    }

    // PUT /exams/{exam}/questions/{question}
    public function updateQuestion(Request $request, Exam $exam, Question $question): JsonResponse
    {
        $teacher = $request->user();

        if (! $this->owns($teacher, $exam)) {
            return $this->error('غير مصرح.', 403);
        }
        if ((int) $question->exam_id !== (int) $exam->id) {
            return $this->error('السؤال لا ينتمي لهذا الامتحان.', 404);
        }

        $data = $request->validate([
            'question_ar'             => ['required', 'string'],
            'question_en'             => ['nullable', 'string'],
            'question_type'           => ['required', 'in:' . implode(',', self::QUESTION_TYPES)],
            'marks'                   => ['required', 'integer', 'min:1'],
            'difficulty'              => ['nullable', 'in:easy,medium,hard'],
            'explanation_ar'          => ['nullable', 'string'],
            'explanation_en'          => ['nullable', 'string'],
            'options'                 => ['required', 'array', 'min:2'],
            'options.*.option_text_ar' => ['required', 'string'],
            'options.*.option_text_en' => ['nullable', 'string'],
            'options.*.is_correct'    => ['boolean'],
        ]);

        if ($error = $this->validateQuestionsShape([$data])) {
            return $this->error($error, 422);
        }

        DB::transaction(function () use ($question, $data) {
            $question->update([
                'question_ar'    => $data['question_ar'],
                'question_en'    => $data['question_en'] ?? null,
                'question_type'  => $data['question_type'],
                'marks'          => $data['marks'],
                'difficulty'     => $data['difficulty'] ?? 'medium',
                'explanation_ar' => $data['explanation_ar'] ?? null,
                'explanation_en' => $data['explanation_en'] ?? null,
            ]);

            $question->options()->delete();
            foreach ($data['options'] as $i => $opt) {
                $question->options()->create([
                    'option_text_ar' => $opt['option_text_ar'],
                    'option_text_en' => $opt['option_text_en'] ?? null,
                    'is_correct'     => (bool) ($opt['is_correct'] ?? false),
                    'order_index'    => $i,
                ]);
            }
        });

        return $this->success($this->questionCard($question->fresh('options')));
    }

    // DELETE /exams/{exam}/questions/{question}
    public function destroyQuestion(Request $request, Exam $exam, Question $question): JsonResponse
    {
        $teacher = $request->user();

        if (! $this->owns($teacher, $exam)) {
            return $this->error('غير مصرح.', 403);
        }
        if ((int) $question->exam_id !== (int) $exam->id) {
            return $this->error('السؤال لا ينتمي لهذا الامتحان.', 404);
        }

        $question->delete();
        $exam->decrement('total_questions');

        return $this->success(null, 'OK');
    }

    private function createQuestion(Exam $exam, array $q, int $orderIndex): Question
    {
        $question = $exam->questions()->create([
            'question_ar'    => $q['question_ar'],
            'question_en'    => $q['question_en'] ?? null,
            'question_type'  => $q['question_type'],
            'marks'          => $q['marks'],
            'difficulty'     => $q['difficulty'] ?? 'medium',
            'explanation_ar' => $q['explanation_ar'] ?? null,
            'explanation_en' => $q['explanation_en'] ?? null,
            'order_index'    => $orderIndex,
        ]);

        foreach ($q['options'] as $i => $opt) {
            $question->options()->create([
                'option_text_ar' => $opt['option_text_ar'],
                'option_text_en' => $opt['option_text_en'] ?? null,
                'is_correct'     => (bool) ($opt['is_correct'] ?? false),
                'order_index'    => $i,
            ]);
        }

        return $question;
    }

    /**
     * Cross-field checks the validator alone can't express: exactly one
     * correct option per question, and true_false questions must have
     * exactly 2 options. Returns an Arabic error message, or null if OK.
     */
    private function validateQuestionsShape(array $questions): ?string
    {
        foreach ($questions as $i => $q) {
            $options      = $q['options'] ?? [];
            $correctCount = collect($options)->filter(fn ($o) => (bool) ($o['is_correct'] ?? false))->count();
            $n            = $i + 1;

            if ($correctCount !== 1) {
                return "السؤال رقم {$n}: يجب تحديد إجابة صحيحة واحدة بالضبط.";
            }
            if (($q['question_type'] ?? null) === 'true_false' && count($options) !== 2) {
                return "السؤال رقم {$n}: سؤال صح/خطأ يجب أن يحتوي على خيارين بالضبط.";
            }
        }

        return null;
    }

    private function classIds(Teacher $teacher)
    {
        return ClassSubject::where('teacher_id', $teacher->id)->pluck('class_id')->unique();
    }

    private function teachesSubjectInClass(Teacher $teacher, int $classId, int $subjectId): bool
    {
        return ClassSubject::where('teacher_id', $teacher->id)
            ->where('class_id', $classId)
            ->where('subject_id', $subjectId)
            ->exists();
    }

    private function owns(Teacher $teacher, Exam $exam): bool
    {
        return (int) $exam->teacher_id === (int) $teacher->id;
    }

    private function examCard(Exam $exam): array
    {
        return [
            'id'                      => $exam->id,
            'title_ar'                => $exam->title_ar,
            'title_en'                => $exam->title_en,
            'description_ar'          => $exam->description_ar,
            'description_en'          => $exam->description_en,
            'exam_type'               => $exam->exam_type,
            'total_questions'         => $exam->questions_count ?? $exam->total_questions,
            'duration_minutes'        => $exam->duration_minutes,
            'total_marks'             => $exam->total_marks,
            'pass_marks'              => $exam->pass_marks,
            'difficulty_level'        => $exam->difficulty_level,
            'is_published'            => $exam->is_published,
            'show_result_immediately' => $exam->show_result_immediately,
            'subject'                 => ['id' => $exam->subject?->id, 'name' => $exam->subject?->name],
            'class'                   => ['id' => $exam->schoolClass?->id, 'name' => $exam->schoolClass?->name],
        ];
    }

    private function questionCard(Question $question): array
    {
        return [
            'id'             => $question->id,
            'question_ar'    => $question->question_ar,
            'question_en'    => $question->question_en,
            'question_type'  => $question->question_type,
            'marks'          => $question->marks,
            'difficulty'     => $question->difficulty,
            'explanation_ar' => $question->explanation_ar,
            'explanation_en' => $question->explanation_en,
            'options'        => $question->options->map(fn ($o) => [
                'id'             => $o->id,
                'option_text_ar' => $o->option_text_ar,
                'option_text_en' => $o->option_text_en,
                'is_correct'     => $o->is_correct,
            ])->values(),
        ];
    }
}
