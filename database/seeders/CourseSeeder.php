<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Category lookup helpers ───────────────────────────────────────

        $primaryRoot = Category::whereNull('parent_id')->where('order_index', 1)->first();
        $tawjihiRoot = Category::whereNull('parent_id')->where('order_index', 2)->first();

        // grade order_index → [sem_order => semester_category_id]
        $semCatIds = [];
        if ($primaryRoot) {
            $grades = Category::where('level', 1)
                ->where('parent_id', $primaryRoot->id)
                ->with(['children' => fn ($q) => $q->orderBy('order_index')])
                ->get();
            foreach ($grades as $grade) {
                foreach ($grade->children as $sem) {
                    $semCatIds[$grade->order_index][$sem->order_index] = $sem->id;
                }
            }
        }

        // stream order_index → [1 => ministry_cat_id]
        $tawjihiMinCatIds = [];
        if ($tawjihiRoot) {
            $streams = Category::where('level', 1)
                ->where('parent_id', $tawjihiRoot->id)
                ->with(['children' => fn ($q) => $q->orderBy('order_index')])
                ->get();
            foreach ($streams as $stream) {
                foreach ($stream->children as $sub) {
                    if ($sub->order_index === 1) {
                        $tawjihiMinCatIds[$stream->order_index] = $sub->id;
                    }
                }
            }
        }

        // Helper: find a subject in a grade semester
        $gradeSubject = function (string $nameAr, int $grade, int $sem) use ($semCatIds): ?Subject {
            $catId = $semCatIds[$grade][$sem] ?? null;
            return $catId ? Subject::where('name_ar', $nameAr)->where('category_id', $catId)->first() : null;
        };

        // Helper: find a compulsory (وزارية) subject in a tawjihi stream
        $tawjihiSubject = function (string $nameAr, int $streamOrder) use ($tawjihiMinCatIds): ?Subject {
            $catId = $tawjihiMinCatIds[$streamOrder] ?? null;
            return $catId ? Subject::where('name_ar', $nameAr)->where('category_id', $catId)->first() : null;
        };

        $courses = [
            // ── Math ─────────────────────────────────────────────────────
            [
                'teacher_email'  => 'mohammad.zayoud@baheth.jo',
                'subject'        => $gradeSubject('الرياضيات', 10, 1),
                'category_id'    => $primaryRoot?->id,
                'title_ar'       => 'الرياضيات الشامل — الصف العاشر',
                'title_en'       => 'Comprehensive Mathematics — Grade 10',
                'description_ar' => 'دورة شاملة تغطي كامل منهج الرياضيات للصف العاشر مع تمارين وزارية وحلول مفصّلة.',
                'description_en' => 'Comprehensive course covering the full Grade 10 Mathematics curriculum with ministry exercises and detailed solutions.',
                'price'          => 25.00,
                'old_price'      => 40.00,
                'total_students' => 820,
                'average_rating' => 4.9,
                'total_videos'   => 60,
                'duration_hours' => 45,
                'difficulty_level' => 'intermediate',
                'is_published'   => true,
                'is_featured'    => true,
                'is_trending'    => true,
                'units'          => [
                    ['title_ar' => 'الدوال وأنواعها',          'title_en' => 'Functions and Types',       'lessons' => 8],
                    ['title_ar' => 'المتتاليات والمتسلسلات',    'title_en' => 'Sequences & Series',        'lessons' => 7],
                    ['title_ar' => 'المصفوفات ومحددات',         'title_en' => 'Matrices & Determinants',   'lessons' => 9],
                    ['title_ar' => 'الاحتمالات والإحصاء',       'title_en' => 'Probability & Statistics',  'lessons' => 7],
                ],
            ],
            [
                'teacher_email'  => 'mohammad.zayoud@baheth.jo',
                'subject'        => $tawjihiSubject('الرياضيات', 1), // health stream
                'category_id'    => $tawjihiRoot?->id,
                'title_ar'       => 'رياضيات التوجيهي — الفرع الصحي (الشامل)',
                'title_en'       => 'Tawjihi Mathematics — Health Track',
                'description_ar' => 'تغطية كاملة لمنهج الرياضيات للفرع الصحي مع نماذج امتحانات وزارية محلولة.',
                'description_en' => 'Full coverage of Tawjihi Mathematics for the Health Track with solved ministry exam models.',
                'price'          => 35.00,
                'old_price'      => 50.00,
                'total_students' => 1240,
                'average_rating' => 4.9,
                'total_videos'   => 80,
                'duration_hours' => 60,
                'difficulty_level' => 'advanced',
                'is_published'   => true,
                'is_featured'    => true,
                'is_trending'    => false,
                'units'          => [
                    ['title_ar' => 'حساب التفاضل والتكامل', 'title_en' => 'Calculus',                 'lessons' => 20],
                    ['title_ar' => 'الدوال المثلثية',         'title_en' => 'Trigonometric Functions',  'lessons' => 12],
                    ['title_ar' => 'الأعداد المركبة',          'title_en' => 'Complex Numbers',          'lessons' => 8],
                    ['title_ar' => 'نماذج وزارية محلولة',     'title_en' => 'Solved Ministry Exams',    'lessons' => 10],
                ],
            ],
            [
                'teacher_email'  => 'mohammad.zayoud@baheth.jo',
                'subject'        => $gradeSubject('الفيزياء', 10, 1),
                'category_id'    => $primaryRoot?->id,
                'title_ar'       => 'الفيزياء المكثّف — الصف العاشر',
                'title_en'       => 'Intensive Physics — Grade 10',
                'description_ar' => 'دورة مكثّفة تشمل القوانين الأساسية في الفيزياء مع تطبيقات عملية وتمارين محلولة.',
                'description_en' => 'An intensive course covering fundamental physics laws with practical applications and solved exercises.',
                'price'          => 20.00,
                'old_price'      => null,
                'total_students' => 540,
                'average_rating' => 4.7,
                'total_videos'   => 40,
                'duration_hours' => 30,
                'difficulty_level' => 'intermediate',
                'is_published'   => true,
                'is_featured'    => false,
                'is_trending'    => true,
                'units'          => [
                    ['title_ar' => 'الحركة والقوى',             'title_en' => 'Motion & Forces',         'lessons' => 10],
                    ['title_ar' => 'الطاقة والشغل',              'title_en' => 'Energy & Work',            'lessons' => 8],
                    ['title_ar' => 'الكهرباء والمغناطيسية',     'title_en' => 'Electricity & Magnetism', 'lessons' => 10],
                ],
            ],
            // ── Arabic ───────────────────────────────────────────────────
            [
                'teacher_email'  => 'sara.namer@baheth.jo',
                'subject'        => $gradeSubject('اللغة العربية', 9, 1),
                'category_id'    => $primaryRoot?->id,
                'title_ar'       => 'اللغة العربية الشامل — الصف التاسع',
                'title_en'       => 'Comprehensive Arabic — Grade 9',
                'description_ar' => 'دورة شاملة في اللغة العربية للصف التاسع: النحو والصرف والأدب والتعبير الكتابي.',
                'description_en' => 'Full Arabic language course for Grade 9: grammar, morphology, literature and composition.',
                'price'          => 18.00,
                'old_price'      => 30.00,
                'total_students' => 690,
                'average_rating' => 4.8,
                'total_videos'   => 50,
                'duration_hours' => 38,
                'difficulty_level' => 'intermediate',
                'is_published'   => true,
                'is_featured'    => true,
                'is_trending'    => false,
                'units'          => [
                    ['title_ar' => 'النحو والصرف',        'title_en' => 'Grammar & Morphology', 'lessons' => 14],
                    ['title_ar' => 'النصوص الأدبية',       'title_en' => 'Literary Texts',       'lessons' => 10],
                    ['title_ar' => 'التعبير والإنشاء',     'title_en' => 'Composition & Writing','lessons' => 8],
                ],
            ],
            [
                'teacher_email'  => 'sara.namer@baheth.jo',
                'subject'        => $tawjihiSubject('اللغة العربية وآدابها', 1), // health stream
                'category_id'    => $tawjihiRoot?->id,
                'title_ar'       => 'عربي توجيهي — الشامل لجميع الفروع',
                'title_en'       => 'Tawjihi Arabic — Comprehensive for All Tracks',
                'description_ar' => 'يغطي جميع مهارات اللغة العربية للتوجيهي: الأدب والنصوص والنحو والبلاغة والتعبير.',
                'description_en' => 'Covers all Tawjihi Arabic skills: literature, texts, grammar, rhetoric and composition.',
                'price'          => 30.00,
                'old_price'      => 45.00,
                'total_students' => 1180,
                'average_rating' => 4.8,
                'total_videos'   => 70,
                'duration_hours' => 55,
                'difficulty_level' => 'advanced',
                'is_published'   => true,
                'is_featured'    => true,
                'is_trending'    => true,
                'units'          => [
                    ['title_ar' => 'الأدب الجاهلي والإسلامي',     'title_en' => 'Pre-Islamic & Islamic Literature', 'lessons' => 12],
                    ['title_ar' => 'الأدب الحديث والمعاصر',        'title_en' => 'Modern Literature',               'lessons' => 10],
                    ['title_ar' => 'النحو التطبيقي',                'title_en' => 'Applied Grammar',                 'lessons' => 14],
                    ['title_ar' => 'البلاغة والنقد',                'title_en' => 'Rhetoric & Criticism',            'lessons' => 8],
                    ['title_ar' => 'التعبير والكتابة الإبداعية',   'title_en' => 'Composition & Creative Writing',  'lessons' => 6],
                ],
            ],
            // ── Chemistry ────────────────────────────────────────────────
            [
                'teacher_email'  => 'khaled.shraida@baheth.jo',
                'subject'        => $gradeSubject('الكيمياء', 10, 1),
                'category_id'    => $primaryRoot?->id,
                'title_ar'       => 'الكيمياء المتقدم — الصف العاشر',
                'title_en'       => 'Advanced Chemistry — Grade 10',
                'description_ar' => 'دورة متقدمة في الكيمياء للصف العاشر مع تجارب عملية مصوّرة وتمارين محلولة.',
                'description_en' => 'Advanced chemistry for Grade 10 with illustrated experiments and solved exercises.',
                'price'          => 22.00,
                'old_price'      => 35.00,
                'total_students' => 460,
                'average_rating' => 4.7,
                'total_videos'   => 45,
                'duration_hours' => 34,
                'difficulty_level' => 'advanced',
                'is_published'   => true,
                'is_featured'    => false,
                'is_trending'    => false,
                'units'          => [
                    ['title_ar' => 'الجدول الدوري والروابط',       'title_en' => 'Periodic Table & Bonding',          'lessons' => 10],
                    ['title_ar' => 'التفاعلات الكيميائية',          'title_en' => 'Chemical Reactions',                'lessons' => 12],
                    ['title_ar' => 'الكيمياء العضوية المقدمة',     'title_en' => 'Introduction to Organic Chemistry', 'lessons' => 10],
                ],
            ],
            [
                'teacher_email'  => 'khaled.shraida@baheth.jo',
                'subject'        => $tawjihiSubject('الكيمياء', 1), // health stream
                'category_id'    => $tawjihiRoot?->id,
                'title_ar'       => 'كيمياء توجيهي — الفرع الصحي والهندسي',
                'title_en'       => 'Tawjihi Chemistry — Health & Engineering Tracks',
                'description_ar' => 'دورة شاملة في كيمياء التوجيهي للفرعين الصحي والهندسي مع نماذج وزارية.',
                'description_en' => 'Comprehensive Tawjihi Chemistry for Health and Engineering tracks with ministry exam models.',
                'price'          => 32.00,
                'old_price'      => 48.00,
                'total_students' => 780,
                'average_rating' => 4.7,
                'total_videos'   => 65,
                'duration_hours' => 50,
                'difficulty_level' => 'advanced',
                'is_published'   => true,
                'is_featured'    => false,
                'is_trending'    => true,
                'units'          => [
                    ['title_ar' => 'الكيمياء العضوية',    'title_en' => 'Organic Chemistry',         'lessons' => 18],
                    ['title_ar' => 'الإلكتروكيمياء',       'title_en' => 'Electrochemistry',           'lessons' => 10],
                    ['title_ar' => 'التوازن الكيميائي',    'title_en' => 'Chemical Equilibrium',       'lessons' => 12],
                    ['title_ar' => 'نماذج وزارية',         'title_en' => 'Ministry Exam Models',       'lessons' => 8],
                ],
            ],
            // ── English ──────────────────────────────────────────────────
            [
                'teacher_email'  => 'rana.jarrar@baheth.jo',
                'subject'        => $gradeSubject('اللغة الإنجليزية', 9, 1),
                'category_id'    => $primaryRoot?->id,
                'title_ar'       => 'اللغة الإنجليزية الشامل — الصف التاسع',
                'title_en'       => 'Comprehensive English — Grade 9',
                'description_ar' => 'دورة شاملة في اللغة الإنجليزية للصف التاسع: القراءة والكتابة والقواعد والمحادثة.',
                'description_en' => 'Complete English course for Grade 9: reading, writing, grammar and conversation.',
                'price'          => 15.00,
                'old_price'      => 25.00,
                'total_students' => 590,
                'average_rating' => 4.8,
                'total_videos'   => 45,
                'duration_hours' => 35,
                'difficulty_level' => 'intermediate',
                'is_published'   => true,
                'is_featured'    => false,
                'is_trending'    => false,
                'units'          => [
                    ['title_ar' => 'قواعد اللغة الإنجليزية',  'title_en' => 'English Grammar',         'lessons' => 14],
                    ['title_ar' => 'مهارات القراءة والفهم',    'title_en' => 'Reading Comprehension',    'lessons' => 10],
                    ['title_ar' => 'مهارات الكتابة',            'title_en' => 'Writing Skills',           'lessons' => 8],
                ],
            ],
            [
                'teacher_email'  => 'rana.jarrar@baheth.jo',
                'subject'        => $tawjihiSubject('اللغة الإنجليزية', 1), // health stream
                'category_id'    => $tawjihiRoot?->id,
                'title_ar'       => 'إنجليزي التوجيهي — الشامل لجميع الفروع',
                'title_en'       => 'Tawjihi English — Comprehensive for All Tracks',
                'description_ar' => 'يشمل القواعد والقراءة والكتابة والمحادثة لمادة الإنجليزية في التوجيهي.',
                'description_en' => 'Covers grammar, reading, writing and speaking for Tawjihi English.',
                'price'          => 28.00,
                'old_price'      => 42.00,
                'total_students' => 960,
                'average_rating' => 4.8,
                'total_videos'   => 60,
                'duration_hours' => 48,
                'difficulty_level' => 'advanced',
                'is_published'   => true,
                'is_featured'    => true,
                'is_trending'    => false,
                'units'          => [
                    ['title_ar' => 'القواعد النحوية المتقدمة', 'title_en' => 'Advanced Grammar',           'lessons' => 16],
                    ['title_ar' => 'القراءة والاستيعاب',        'title_en' => 'Reading & Comprehension',    'lessons' => 12],
                    ['title_ar' => 'الكتابة الأكاديمية',        'title_en' => 'Academic Writing',           'lessons' => 10],
                    ['title_ar' => 'نماذج وزارية محلولة',       'title_en' => 'Solved Ministry Models',     'lessons' => 8],
                ],
            ],
        ];

        foreach ($courses as $data) {
            $teacherEmail = $data['teacher_email'];
            $subject      = $data['subject'];
            $categoryId   = $data['category_id'];
            $units        = $data['units'];

            unset($data['teacher_email'], $data['subject'], $data['category_id'], $data['units']);

            $teacher = Teacher::where('email', $teacherEmail)->first();

            if (! $teacher || ! $subject) continue;

            $course = Course::updateOrCreate(
                ['title_ar' => $data['title_ar']],
                array_merge($data, [
                    'teacher_id'  => $teacher->id,
                    'subject_id'  => $subject->id,
                    'category_id' => $categoryId,
                    'is_free'     => false,
                ])
            );

            foreach ($units as $i => $unitData) {
                $unit = Unit::updateOrCreate(
                    ['course_id' => $course->id, 'order_index' => $i + 1],
                    [
                        'title_ar'    => $unitData['title_ar'],
                        'title_en'    => $unitData['title_en'],
                        'order_index' => $i + 1,
                    ]
                );

                for ($j = 1; $j <= $unitData['lessons']; $j++) {
                    Lesson::updateOrCreate(
                        ['unit_id' => $unit->id, 'order_index' => $j],
                        [
                            'title_ar'    => "الدرس {$j}: {$unitData['title_ar']}",
                            'title_en'    => "Lesson {$j}: {$unitData['title_en']}",
                            'lesson_type' => 'video',
                            'order_index' => $j,
                            'is_free'     => ($j === 1 && $i === 0),
                            'is_published'=> true,
                        ]
                    );
                }
            }
        }
    }
}
