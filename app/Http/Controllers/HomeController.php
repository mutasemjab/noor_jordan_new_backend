<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Student;
use App\Models\StudentAnswer;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $stats = [
            'students'     => Student::count() ?: 5000,
            'teachers'     => Teacher::where('is_active', true)->count() ?: 120,
            'satisfaction' => 98,
        ];

        $teachers = Teacher::where('is_active', true)
            ->orderByDesc('total_students')
            ->limit(4)
            ->get();

        $leaderboard = ExamAttempt::with('student')
            ->where('status', 'submitted')
            ->where('submitted_at', '>=', now()->startOfWeek())
            ->orderByDesc('percentage')
            ->limit(3)
            ->get();

        return view('front.home', compact('stats', 'teachers', 'leaderboard'));
    }

    public function contact(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:200',
            'email'   => 'required|email|max:200',
            'phone'   => 'nullable|string|max:30',
            'subject' => 'nullable|string|max:200',
            'message' => 'required|string|max:3000',
        ]);

        $nameParts = explode(' ', trim($validated['name']), 2);

        ContactMessage::create([
            'first_name' => $nameParts[0],
            'last_name'  => $nameParts[1] ?? '',
            'email'      => $validated['email'],
            'phone'      => $validated['phone'] ?? null,
            'subject'    => $validated['subject'] ?? 'General',
            'message'    => $validated['message'],
            'status'     => 'new',
        ]);

        return back()->with('contact_success', true);
    }

    public function exams(Request $request)
    {
        $query = Exam::where('is_published', true);

        if ($request->filled('type')) {
            $query->where('exam_type', $request->type);
        }

        $exams = $query->orderByDesc('total_attempts')->paginate(12)->withQueryString();

        $leaderboard = ExamAttempt::with('student')
            ->where('status', 'submitted')
            ->where('submitted_at', '>=', now()->startOfWeek())
            ->orderByDesc('percentage')
            ->limit(10)
            ->get();

        return view('front.exams', compact('exams', 'leaderboard'));
    }

    public function examShow(int $id)
    {
        $exam = Exam::with(['subject'])
            ->where('is_published', true)
            ->findOrFail($id);

        return view('front.exam-show', compact('exam'));
    }

    public function examTake(int $id)
    {
        if (! auth('student')->check()) {
            return redirect()->route('student.login');
        }

        $exam = Exam::with(['questions.options'])
            ->where('is_published', true)
            ->findOrFail($id);

        if ($exam->questions->isEmpty()) {
            return redirect()->route('exams.show', $id)
                ->with('error', app()->getLocale() === 'ar' ? 'لا توجد أسئلة في هذا الامتحان بعد.' : 'This exam has no questions yet.');
        }

        $questions = $exam->shuffle_questions
            ? $exam->questions->shuffle()
            : $exam->questions;

        if ($exam->shuffle_options) {
            $questions = $questions->map(function ($q) {
                $q->setRelation('options', $q->options->shuffle());
                return $q;
            });
        }

        ExamAttempt::where('student_id', auth('student')->id())
            ->where('exam_id', $exam->id)
            ->where('status', 'in_progress')
            ->when($exam->duration_minutes, fn ($q) =>
                $q->where('started_at', '<', now()->subMinutes($exam->duration_minutes + 5))
            )
            ->update(['status' => 'submitted', 'submitted_at' => now()]);

        $attempt = ExamAttempt::firstOrCreate(
            ['student_id' => auth('student')->id(), 'exam_id' => $exam->id, 'status' => 'in_progress'],
            ['started_at' => now()]
        );

        return view('front.exam-take', compact('exam', 'questions', 'attempt'));
    }

    public function examSubmit(Request $request, int $id)
    {
        if (! auth('student')->check()) {
            return redirect()->route('student.login');
        }

        $exam = Exam::with(['questions.options'])->where('is_published', true)->findOrFail($id);

        $attempt = ExamAttempt::where('student_id', auth('student')->id())
            ->where('exam_id', $exam->id)
            ->where('status', 'in_progress')
            ->firstOrFail();

        $answers    = $request->input('answers', []);
        $score      = 0;
        $totalMarks = 0;

        DB::transaction(function () use ($exam, $attempt, $answers, &$score, &$totalMarks) {
            StudentAnswer::where('attempt_id', $attempt->id)->delete();

            foreach ($exam->questions as $question) {
                $totalMarks += $question->marks;
                $selectedId  = $answers[$question->id] ?? null;

                if (! $selectedId) {
                    StudentAnswer::create([
                        'attempt_id'         => $attempt->id,
                        'question_id'        => $question->id,
                        'selected_option_id' => null,
                        'is_correct'         => false,
                        'marks_earned'       => 0,
                    ]);
                    continue;
                }

                $correctOption = $question->options->firstWhere('is_correct', true);
                $isCorrect     = $correctOption && (int) $selectedId === $correctOption->id;

                if ($isCorrect) {
                    $score += $question->marks;
                }

                StudentAnswer::create([
                    'attempt_id'         => $attempt->id,
                    'question_id'        => $question->id,
                    'selected_option_id' => (int) $selectedId,
                    'is_correct'         => $isCorrect,
                    'marks_earned'       => $isCorrect ? $question->marks : 0,
                ]);
            }

            $percentage = $totalMarks > 0 ? round(($score / $totalMarks) * 100, 2) : 0;
            $passMarks  = $exam->pass_marks ?? ($totalMarks * 0.5);

            $attempt->update([
                'status'             => 'submitted',
                'score'              => $score,
                'total_marks'        => $totalMarks,
                'percentage'         => $percentage,
                'is_passed'          => $score >= $passMarks,
                'time_taken_seconds' => (int) request('time_taken_seconds', 0),
                'submitted_at'       => now(),
            ]);

            $exam->increment('total_attempts');
            $avg = ExamAttempt::where('exam_id', $exam->id)
                ->where('status', 'submitted')
                ->avg('percentage');
            $exam->update(['average_success_rate' => round($avg)]);
        });

        return redirect()->route('exams.result', [$id, $attempt->id]);
    }

    public function examResult(int $examId, int $attemptId)
    {
        if (! auth('student')->check()) {
            return redirect()->route('student.login');
        }

        $exam    = Exam::with(['questions.options'])->findOrFail($examId);
        $attempt = ExamAttempt::with(['answers.selectedOption', 'answers.question.options'])
            ->where('student_id', auth('student')->id())
            ->findOrFail($attemptId);

        return view('front.exam-result', compact('exam', 'attempt'));
    }

    public function teacherProfile(int $id)
    {
        $teacher = Teacher::with(['subjects'])
            ->where('is_active', true)
            ->findOrFail($id);

        return view('front.teacher-profile', compact('teacher'));
    }
}
