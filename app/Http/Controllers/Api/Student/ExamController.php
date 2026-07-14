<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Question;
use App\Models\StudentAnswer;
use App\Services\ExamService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExamController extends Controller
{
    use ApiResponse;

    public function __construct(private ExamService $service) {}

    // GET /exams
    public function index(Request $request): JsonResponse
    {
        $filters = array_merge(
            $request->only(['course_id', 'subject_id', 'exam_type', 'search']),
            ['is_published' => true]
        );

        $paginated = $this->service->list($filters, 15);

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

    // GET /exams/{id}
    public function show(int $id): JsonResponse
    {
        $exam = $this->service->find($id);

        if (! $exam->is_published) {
            return $this->error('الامتحان غير متاح', 404);
        }

        $questions = $exam->shuffle_questions
            ? $exam->questions->shuffle()
            : $exam->questions;

        $data = $this->examCard($exam);
        $data['questions'] = $questions->map(fn ($q) => [
            'id'            => $q->id,
            'question_text' => $q->question_text,
            'question_ar'   => $q->question_ar,
            'question_en'   => $q->question_en,
            'question_type' => $q->question_type,
            'marks'         => $q->marks,
            'image'         => $q->image ? asset('assets/uploads/questions/' . $q->image) : null,
            'options'       => ($exam->shuffle_options ? $q->options->shuffle() : $q->options)
                ->map(fn ($o) => [
                    'id'          => $o->id,
                    'option_text' => $o->option_text,
                    'option_ar'   => $o->option_text_ar,
                    'option_en'   => $o->option_text_en,
                    // never expose is_correct here
                ])->values(),
        ])->values();

        return $this->success($data);
    }

    // POST /exams/{id}/start  [auth]
    public function start(Request $request, int $id): JsonResponse
    {
        $exam = Exam::where('is_published', true)->findOrFail($id);
        $student = $request->user();

        // Return existing in-progress attempt if any
        $existing = ExamAttempt::where('student_id', $student->id)
            ->where('exam_id', $id)
            ->where('status', 'in_progress')
            ->first();

        if ($existing) {
            return $this->success([
                'attempt_id' => $existing->id,
                'started_at' => $existing->started_at,
                'exam'       => $this->examCard($exam),
            ], 'محاولة جارية');
        }

        $attempt = ExamAttempt::create([
            'exam_id'    => $id,
            'student_id' => $student->id,
            'status'     => 'in_progress',
            'started_at' => now(),
            'total_marks'=> $exam->total_marks,
        ]);

        $exam->increment('total_attempts');

        return $this->success([
            'attempt_id' => $attempt->id,
            'started_at' => $attempt->started_at,
            'exam'       => $this->examCard($exam),
        ], 'تم بدء الامتحان', 201);
    }

    // POST /attempts/{attempt}/submit  [auth]
    public function submit(Request $request, int $attemptId): JsonResponse
    {
        $attempt = ExamAttempt::where('student_id', $request->user()->id)
            ->where('status', 'in_progress')
            ->findOrFail($attemptId);

        $request->validate([
            'answers'                => ['required', 'array'],
            'answers.*.question_id'  => ['required', 'exists:questions,id'],
            'answers.*.option_id'    => ['required', 'exists:question_options,id'],
        ]);

        $exam = Exam::with('questions.options')->findOrFail($attempt->exam_id);

        DB::transaction(function () use ($attempt, $exam, $request) {
            $score = 0;

            foreach ($request->answers as $ans) {
                $question = $exam->questions->find($ans['question_id']);
                if (! $question) {
                    continue;
                }

                $option      = $question->options->find($ans['option_id']);
                $isCorrect   = $option && $option->is_correct;
                $marksEarned = $isCorrect ? ($question->marks ?? 1) : 0;

                StudentAnswer::updateOrCreate(
                    ['attempt_id' => $attempt->id, 'question_id' => $ans['question_id']],
                    [
                        'selected_option_id' => $ans['option_id'],
                        'is_correct'         => $isCorrect,
                        'marks_earned'       => $marksEarned,
                    ]
                );

                $score += $marksEarned;
            }

            $totalMarks  = $exam->total_marks ?: $exam->total_questions;
            $percentage  = $totalMarks > 0 ? round(($score / $totalMarks) * 100, 2) : 0;
            $isPassed    = $percentage >= ($exam->pass_marks ?? 50);
            // store as seconds (matches actual column name)
            $timeTakenSeconds = now()->diffInSeconds($attempt->started_at);

            $attempt->update([
                'score'               => $score,
                'total_marks'         => $totalMarks,
                'percentage'          => $percentage,
                'time_taken_seconds'  => $timeTakenSeconds,
                'status'              => 'submitted',
                'is_passed'           => $isPassed,
                'submitted_at'        => now(),
            ]);
        });

        $attempt->refresh();

        // Compute stats from student_answers (source of truth)
        $answers = $attempt->answers()->with('question.options')->get();

        $correct   = $answers->where('is_correct', true)->count();
        $wrong     = $answers->where('is_correct', false)->count();
        $unanswered = max(0, ($exam->total_questions ?? 0) - $answers->count());

        $result = [
            'attempt_id'      => $attempt->id,
            'score'           => $attempt->score,
            'total_marks'     => $attempt->total_marks,
            'percentage'      => (float) $attempt->percentage,
            'correct_answers' => $correct,
            'wrong_answers'   => $wrong,
            'unanswered'      => $unanswered,
            'is_passed'       => $attempt->is_passed,
            'time_taken_minutes' => (int) ceil($attempt->time_taken_seconds / 60),
        ];

        // Show correct answers if exam setting allows
        if ($exam->show_result_immediately) {
            $result['answers'] = $answers->map(fn ($a) => [
                'question_id'        => $a->question_id,
                'question_text'      => $a->question?->question_text,
                'selected_option_id' => $a->selected_option_id,
                'is_correct'         => $a->is_correct,
                'marks_earned'       => $a->marks_earned,
                'correct_option_id'  => $a->question?->correctOption?->id,
                'correct_option'     => $a->question?->correctOption?->option_text,
                'explanation'        => $a->question?->explanation,
            ])->values();
        }

        return $this->success($result, $attempt->is_passed ? 'أحسنت! لقد اجتزت الامتحان' : 'تم تسليم الامتحان');
    }

    private function examCard(Exam $exam): array
    {
        return [
            'id'                      => $exam->id,
            'title'                   => $exam->title,
            'title_ar'                => $exam->title_ar,
            'title_en'                => $exam->title_en,
            'description'             => $exam->description,
            'exam_type'               => $exam->exam_type,
            'total_questions'         => $exam->total_questions,
            'duration_minutes'        => $exam->duration_minutes,
            'total_marks'             => $exam->total_marks,
            'pass_marks'              => $exam->pass_marks,
            'difficulty_level'        => $exam->difficulty_level,
            'show_result_immediately' => $exam->show_result_immediately,
            'course'  => $exam->course?->only(['id', 'title_ar', 'title_en']),
            'subject' => ['id' => $exam->subject?->id, 'name' => $exam->subject?->name],
        ];
    }
}
