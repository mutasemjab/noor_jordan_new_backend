<?php

namespace App\Http\Controllers;

use App\Models\CardNumber;
use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Student;
use App\Models\StudentAnswer;
use App\Models\Subject;
use App\Models\Teacher;
use App\Services\ProgressService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $stats = [
            'students'     => Student::count() ?: 2400,
            'courses'      => Course::where('is_published', true)->count() ?: 120,
            'teachers'     => Teacher::where('is_active', true)->count() ?: 120,
            'satisfaction' => 98,
        ];

        $categories = Category::active()
            ->withCount(['courses' => fn ($q) => $q->where('is_published', true)])
            ->orderBy('order_index')
            ->limit(4)
            ->get();

        $allFeatured = Course::with(['teacher', 'category'])
            ->where('is_published', true)
            ->withCount('enrollments')
            ->latest()
            ->limit(12)
            ->get();

        $courses = $allFeatured->take(6)->map(function ($course) use ($allFeatured) {
            $tags = [];
            if ($allFeatured->sortByDesc('enrollments_count')->take(4)->contains($course)) $tags[] = 'popular';
            if ($allFeatured->sortByDesc('created_at')->take(4)->contains($course))         $tags[] = 'trending';
            $course->filter_tags = implode(' ', $tags) ?: 'popular';
            return $course;
        });

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

        $overlayData = $this->buildOverlayData();

        return view('front.home', compact(
            'stats', 'categories', 'courses', 'teachers', 'leaderboard', 'overlayData'
        ));
    }

    // ── Overlay SPA data ──────────────────────────────────────────────────

    private function buildOverlayData(): array
    {
        // ── Grades from category tree ─────────────────────────────────────
        // Load root "primary-grades" with grade children and their subjects.
        // Subjects linked directly to the grade category (level 1).
        // Subjects under semester sub-categories (level 2) are also collected.

        $primaryRoot = Category::with([
            'children' => fn ($q) => $q->where('is_active', true)->orderBy('order_index'),
            'children.subjects' => fn ($q) => $q->where('is_active', true)->orderBy('order_index'),
            'children.children' => fn ($q) => $q->where('is_active', true)->orderBy('order_index'),
            'children.children.subjects' => fn ($q) => $q->where('is_active', true)->orderBy('order_index'),
        ])->whereNull('parent_id')->where('order_index', 1)->first();

        // Collect all subject IDs (subjects are under semester categories, not grade directly)
        $subjectIds = [];
        if ($primaryRoot) {
            foreach ($primaryRoot->children as $grade) {
                foreach ($grade->children as $sem) {
                    foreach ($sem->subjects as $s) $subjectIds[] = $s->id;
                }
            }
        }

        $courseCounts = Subject::whereIn('id', array_unique($subjectIds))
            ->withCount(['courses' => fn ($q) => $q->where('is_published', true)])
            ->get()->keyBy('id')->map(fn ($s) => $s->courses_count);

        $toChip = fn ($s) => [
            'id'           => $s->id,
            'l'            => $s->name_ar,
            'l_en'         => $s->name_en,
            'e'            => $s->icon  ?? '📚',
            'bg'           => $s->color_class ?? 'si-blue',
            'courses_count'=> $courseCounts[$s->id] ?? 0,
        ];

        $grades = [];
        if ($primaryRoot) {
            foreach ($primaryRoot->children as $grade) {
                // Build per-semester subject lists keyed by semester order_index (1 or 2)
                $semesters = [];
                $allSubjects = collect();
                foreach ($grade->children as $sem) {
                    $semSubjects = $sem->subjects->map($toChip)->values();
                    $semesters[$sem->order_index] = $semSubjects;
                    $allSubjects = $allSubjects->concat($sem->subjects);
                }

                $grades[] = [
                    'n'        => $grade->order_index,  // 1..10 — used by overlay JS ORDINALS array
                    'label'    => $grade->name_ar,
                    'label_en' => $grade->name_en,
                    'stage'    => $primaryRoot->name_ar,
                    'semesters'=> $semesters,
                    'subjects' => $allSubjects->unique('id')->map($toChip)->values(), // for badge count
                ];
            }
        }

        // ── Tawjihi streams from category tree ───────────────────────────
        // Root: Tawjihi (level=0, order_index=2)
        // Children: streams (level=1), each with two sub-cats:
        //   order_index=1 → مواد وزارية (is_elective=false)
        //   order_index=2 → مواد مدرسية  (is_elective=true)

        $tawjihiRoot = Category::with([
            'children'                   => fn ($q) => $q->where('is_active', true)->orderBy('order_index'),
            'children.children'          => fn ($q) => $q->where('is_active', true)->orderBy('order_index'),
            'children.children.subjects' => fn ($q) => $q->where('is_active', true)->orderBy('order_index'),
        ])->whereNull('parent_id')->where('order_index', 2)->first();

        $fields = collect();
        if ($tawjihiRoot) {
            foreach ($tawjihiRoot->children as $stream) {
                $allSubjects = collect();
                foreach ($stream->children as $sub) {
                    $allSubjects = $allSubjects->concat($sub->subjects);
                }
                $fields->push([
                    'id'       => $stream->id,
                    'label'    => $stream->name_ar,
                    'label_en' => $stream->name_en,
                    'icon'     => $stream->icon,
                    'sub'      => null,
                    'sub_en'   => null,
                    'comp'     => $allSubjects->where('is_elective', false)->map(fn ($s) => $toChip($s))->values(),
                    'elec'     => $allSubjects->where('is_elective', true)->map(fn ($s) => $toChip($s))->values(),
                ]);
            }
        }
        $fields = $fields->values();

        // ── Previous-year exam generations ────────────────────────────────

        $generations = Exam::where('exam_type', 'previous_years')
            ->where('is_published', true)
            ->whereNotNull('academic_year')
            ->distinct()
            ->orderBy('academic_year')
            ->pluck('academic_year')
            ->map(fn ($year) => [
                'year'  => (string) $year,
                'label' => 'جيل ' . $year,
                'pill'  => 'متاح الآن',
                'hot'   => true,
            ])
            ->values()
            ->toArray();

        return [
            'grades'      => $grades,
            'fields'      => $fields,
            'generations' => $generations,
            'coursesUrl'  => route('courses.index'),
        ];
    }

    // ── Public pages ──────────────────────────────────────────────────────

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

    public function courses(Request $request)
    {
        $categories = Category::active()->orderBy('order_index')->get();

        $query = Course::with(['teacher', 'category'])
            ->where('is_published', true);

        if ($request->filled('category')) {
            $query->where('category_id', (int) $request->category);
        }

        if ($request->filled('subject')) {
            $query->where('subject_id', (int) $request->subject);
        }

        if ($request->filled('q')) {
            $term = $request->q;
            $query->where(fn ($q) => $q
                ->where('title_ar', 'like', "%{$term}%")
                ->orWhere('title_en', 'like', "%{$term}%")
            );
        }

        match ($request->get('sort', 'popular')) {
            'newest'    => $query->latest(),
            'top-rated' => $query->orderByDesc('average_rating'),
            'cheap'     => $query->orderBy('price'),
            default     => $query->orderByDesc('total_students'),
        };

        $courses = $query->paginate(12)->withQueryString();

        return view('front.courses', compact('courses', 'categories'));
    }

    public function courseDetail(int $id)
    {
        $course = Course::with([
                'teacher', 'category',
                'units' => fn ($q) => $q->orderBy('order_index'),
                'units.lessons' => fn ($q) => $q->orderBy('order_index'),
                'exams' => fn ($q) => $q->where('is_published', true),
            ])
            ->where('id', $id)
            ->where('is_published', true)
            ->firstOrFail();

        $relatedCourses = Course::with('teacher')
            ->where('is_published', true)
            ->where('category_id', $course->category_id)
            ->where('id', '!=', $course->id)
            ->limit(3)
            ->get();

        $isEnrolled = auth('student')->check()
            && Enrollment::where('student_id', auth('student')->id())
                ->where('course_id', $course->id)
                ->where('is_active', true)
                ->exists();

        // Build exam placement maps for curriculum display
        $lessonExams  = $course->exams->whereNotNull('lesson_id')->keyBy('lesson_id');
        $unitEndExams = $course->exams->whereNull('lesson_id')->whereNotNull('unit_id')->keyBy('unit_id');
        $courseExams  = $course->exams->whereNull('lesson_id')->whereNull('unit_id');

        return view('front.course-detail', compact('course', 'relatedCourses', 'isEnrolled', 'lessonExams', 'unitEndExams', 'courseExams'));
    }

    public function activateCourse(Request $request, int $id)
    {
        if (! auth('student')->check()) {
            return redirect()->route('student.login');
        }

        $request->validate(['card_number' => 'required|string|max:100']);

        $course = Course::where('id', $id)->where('is_published', true)->firstOrFail();

        $isAr = app()->getLocale() === 'ar';

        // Already enrolled?
        if (Enrollment::where('student_id', auth('student')->id())
                ->where('course_id', $course->id)->exists()) {
            return redirect()->route('courses.show', $id)
                ->with('activation_success', $isAr ? 'أنت مسجّل في هذه الدورة بالفعل.' : 'You are already enrolled in this course.')
                ->with('activated_course', $course->id);
        }

        $cardNumber = CardNumber::where('number', trim($request->card_number))
            ->where('activate', 1) // active
            ->where('status', 2)   // not used
            ->where('sell', 1)     // sold
            ->first();

        if (! $cardNumber) {
            return redirect()->route('courses.show', $id)
                ->with('activation_error', $isAr ? 'رقم الكارت غير صحيح أو تم استخدامه مسبقاً.' : 'Invalid card number or already used.')
                ->with('error_course', $course->id);
        }

        DB::transaction(function () use ($course, $cardNumber) {
            Enrollment::create([
                'student_id'           => auth('student')->id(),
                'course_id'            => $course->id,
                'enrolled_at'          => now(),
                'is_active'            => true,
                'is_completed'         => false,
                'progress_percentage'  => 0,
            ]);

            $cardNumber->update([
                'status'           => 1,
                'assigned_user_id' => auth('student')->id(),
            ]);
        });

        return redirect()->route('courses.show', $id)
            ->with('activation_success', $isAr ? 'تم تفعيل الدورة بنجاح! يمكنك البدء الآن.' : 'Course activated successfully! You can start now.')
            ->with('activated_course', $course->id);
    }

    public function exams(Request $request)
    {
        $query = Exam::where('is_published', true)->whereNull('course_id');

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
        $exam = Exam::with(['subject', 'course'])
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

        // Shuffle if configured
        $questions = $exam->shuffle_questions
            ? $exam->questions->shuffle()
            : $exam->questions;

        if ($exam->shuffle_options) {
            $questions = $questions->map(function ($q) {
                $q->setRelation('options', $q->options->shuffle());
                return $q;
            });
        }

        // Close any stuck in_progress attempts older than the exam duration, then create fresh
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

        $answers   = $request->input('answers', []);
        $score     = 0;
        $correct   = 0;
        $wrong     = 0;
        $unanswered = 0;
        $totalMarks = 0;

        DB::transaction(function () use ($exam, $attempt, $answers, &$score, &$correct, &$wrong, &$unanswered, &$totalMarks) {
            // Remove any previous answers for this attempt
            StudentAnswer::where('attempt_id', $attempt->id)->delete();

            foreach ($exam->questions as $question) {
                $totalMarks += $question->marks;
                $selectedId  = $answers[$question->id] ?? null;

                if (! $selectedId) {
                    $unanswered++;
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
                    $correct++;
                } else {
                    $wrong++;
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

            // Update exam stats
            $exam->increment('total_attempts');
            $avg = ExamAttempt::where('exam_id', $exam->id)
                ->where('status', 'submitted')
                ->avg('percentage');
            $exam->update(['average_success_rate' => round($avg)]);
        });

        $attempt->refresh();

        // Recalculate course progress if this is a course exam and student is enrolled
        if ($exam->course_id && auth('student')->check()) {
            $enrolled = Enrollment::where('student_id', auth('student')->id())
                ->where('course_id', $exam->course_id)
                ->where('is_active', true)
                ->exists();
            if ($enrolled) {
                app(ProgressService::class)->recalculate(auth('student')->id(), $exam->course_id);
            }
        }

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
        $teacher = Teacher::with([
                'courses' => fn ($q) => $q
                    ->where('is_published', true)
                    ->withCount('enrollments')
                    ->orderByDesc('total_students')
                    ->limit(6),
                'subjects',
            ])
            ->where('is_active', true)
            ->findOrFail($id);

        return view('front.teacher-profile', compact('teacher'));
    }
}
