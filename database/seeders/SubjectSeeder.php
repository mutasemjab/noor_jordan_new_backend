<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        // ── Basic-grade subjects (per SEMESTER) ──────────────────────────
        // Each subject is linked to a semester category (level 2), not the grade.
        // [name_ar, name_en, icon, color_class, grades[], order]

        $subjectDefs = [
            // Grades 1–4
            ['اللغة العربية',      'Arabic Language',   '📖', 'si-blue',   [1,2,3,4],   1],
            ['اللغة الإنجليزية',   'English Language',  '🌐', 'si-cyan',   [1,2,3,4],   2],
            ['الرياضيات',          'Mathematics',       '📐', 'si-blue',   [1,2,3,4],   3],
            ['العلوم',             'Science',           '🔬', 'si-green',  [1,2,3],     4],
            ['العلوم العامة',      'General Science',   '🧪', 'si-green',  [4],         4],
            ['التربية الإسلامية',  'Islamic Education', '☪️', 'si-gold',   [1,2,3,4],   5],
            ['التربية الوطنية',    'Civic Education',   '🏛️', 'si-green',  [1,2,3,4],   6],
            ['التربية الفنية',     'Art Education',     '🎨', 'si-red',    [1,2,3,4],   7],
            ['التربية البدنية',    'Physical Education','⚽', 'si-green',  [1,2,3,4],   8],
            // Grades 5–6
            ['اللغة العربية',      'Arabic Language',   '📖', 'si-blue',   [5,6],       1],
            ['اللغة الإنجليزية',   'English Language',  '🌐', 'si-cyan',   [5,6],       2],
            ['الرياضيات',          'Mathematics',       '📐', 'si-blue',   [5,6],       3],
            ['العلوم العامة',      'General Science',   '🧪', 'si-green',  [5,6],       4],
            ['التربية الإسلامية',  'Islamic Education', '☪️', 'si-gold',   [5,6],       5],
            ['الدراسات الاجتماعية','Social Studies',    '🗺️', 'si-gold',   [5,6],       6],
            ['الحاسوب',            'Computer Science',  '💻', 'si-blue',   [5,6],       7],
            ['التربية الفنية',     'Art Education',     '🎨', 'si-red',    [5,6],       8],
            ['التربية البدنية',    'Physical Education','⚽', 'si-green',  [5,6],       9],
            // Grades 7–9
            ['اللغة العربية',      'Arabic Language',   '📖', 'si-blue',   [7,8,9],     1],
            ['اللغة الإنجليزية',   'English Language',  '🌐', 'si-cyan',   [7,8,9],     2],
            ['الرياضيات',          'Mathematics',       '📐', 'si-blue',   [7,8,9],     3],
            ['الفيزياء',           'Physics',           '⚛️', 'si-blue',   [7,8,9],     4],
            ['الكيمياء',           'Chemistry',         '⚗️', 'si-red',    [7,8,9],     5],
            ['الأحياء',            'Biology',           '🧬', 'si-green',  [7,8,9],     6],
            ['التربية الإسلامية',  'Islamic Education', '☪️', 'si-gold',   [7,8,9],     7],
            ['التاريخ',            'History',           '🏛️', 'si-gold',   [7,8,9],     8],
            ['الجغرافيا',          'Geography',         '🌍', 'si-green',  [7,8,9],     9],
            ['الحاسوب',            'Computer Science',  '💻', 'si-blue',   [7,8,9],     10],
            // Grade 10
            ['اللغة العربية',      'Arabic Language',   '📖', 'si-blue',   [10],        1],
            ['اللغة الإنجليزية',   'English Language',  '🌐', 'si-cyan',   [10],        2],
            ['الرياضيات',          'Mathematics',       '📐', 'si-blue',   [10],        3],
            ['الفيزياء',           'Physics',           '⚛️', 'si-blue',   [10],        4],
            ['الكيمياء',           'Chemistry',         '⚗️', 'si-red',    [10],        5],
            ['الأحياء',            'Biology',           '🧬', 'si-green',  [10],        6],
            ['التربية الإسلامية',  'Islamic Education', '☪️', 'si-gold',   [10],        7],
            ['التاريخ',            'History',           '🏛️', 'si-gold',   [10],        8],
            ['الجغرافيا',          'Geography',         '🌍', 'si-green',  [10],        9],
            ['الحاسوب',            'Computer Science',  '💻', 'si-blue',   [10],        10],
            ['التربية الوطنية',    'Civic Education',   '🏛️', 'si-green',  [10],        11],
        ];

        // Build lookup: grade_order_index → [1 => sem1_cat_id, 2 => sem2_cat_id]
        $gradeCategories = Category::where('level', 1)
            ->whereHas('parent', fn ($q) => $q->where('name_en', 'Basic Grades'))
            ->with(['children' => fn ($q) => $q->orderBy('order_index')])
            ->orderBy('order_index')
            ->get();

        $semCatIds = [];
        foreach ($gradeCategories as $grade) {
            foreach ($grade->children as $sem) {
                $semCatIds[$grade->order_index][$sem->order_index] = $sem->id;
            }
        }

        foreach ($subjectDefs as [$nameAr, $nameEn, $icon, $color, $grades, $order]) {
            foreach ($grades as $grade) {
                foreach ([1, 2] as $sem) {
                    $catId = $semCatIds[$grade][$sem] ?? null;
                    Subject::updateOrCreate(
                        ['name_ar' => $nameAr, 'category_id' => $catId],
                        [
                            'name_en'          => $nameEn,
                            'icon'             => $icon,
                            'color_class'      => $color,
                            'is_elective'      => false,
                            'order_index'      => $order,
                            'is_active'        => true,
                        ]
                    );
                }
            }
        }

        // ── Tawjihi subjects ─────────────────────────────────────────────
        // is_elective=false → مواد وزارية (sub-cat order_index=1 under each stream)
        // is_elective=true  → مواد مدرسية  (sub-cat order_index=2 under each stream)
        //
        // Pairs use stream order_index (1=health, 2=engineering, 3=business, 4=humanities)

        // Stream categories (level 1 under Tawjihi), each with ministry/school children
        $streamCategories = Category::where('level', 1)
            ->whereHas('parent', fn ($q) => $q->where('name_en', 'Tawjihi'))
            ->with(['children' => fn ($q) => $q->orderBy('order_index')])
            ->orderBy('order_index')
            ->get()
            ->keyBy('order_index');

        // stream_order → [1 => ministry_cat_id, 2 => school_cat_id]
        $streamSubCatIds = [];
        foreach ($streamCategories as $streamOrder => $stream) {
            foreach ($stream->children as $sub) {
                $streamSubCatIds[$streamOrder][$sub->order_index] = $sub->id;
            }
        }

        // [name_ar, name_en, icon, color, is_elective, [stream_orders...], order]
        // stream orders: 1=health, 2=engineering, 3=business, 4=humanities
        $tawjihiDefs = [
            // Compulsory in all 4 streams
            ['اللغة العربية وآدابها', 'Arabic Language & Lit.',  '📖', 'si-blue',  false, [1,2,3,4], 1],
            ['اللغة الإنجليزية',     'English Language',         '🌐', 'si-cyan',  false, [1,2,3,4], 2],
            ['التربية الإسلامية',    'Islamic Education',        '☪️', 'si-gold',  false, [1,2,3,4], 3],
            ['التربية الوطنية',      'Civic Education',          '🏛️', 'si-green', false, [1,2,3,4], 4],
            // Math: health(1), engineering(2), business(3)
            ['الرياضيات',            'Mathematics',              '📐', 'si-blue',  false, [1,2,3],   5],
            // Physics & Chemistry: health(1) + engineering(2)
            ['الفيزياء',             'Physics',                  '⚛️', 'si-blue',  false, [1,2],     6],
            ['الكيمياء',             'Chemistry',                '⚗️', 'si-red',   false, [1,2],     7],
            // Biology: health(1) only
            ['الأحياء',              'Biology',                  '🧬', 'si-green', false, [1],       8],
            // Business(3) compulsory
            ['الاقتصاد',             'Economics',                '📊', 'si-gold',  false, [3],       6],
            ['مبادئ المحاسبة',       'Principles of Accounting', '🧾', 'si-gold',  false, [3],       7],
            // Humanities(4) compulsory
            ['التاريخ',              'History',                  '🏛️', 'si-gold',  false, [4],       5],
            ['الجغرافيا',            'Geography',                '🌍', 'si-green', false, [4],       6],
            ['الثقافة العامة',       'General Culture',          '🌐', 'si-blue',  false, [4],       7],
            // School (elective) — health(1)
            ['علم الأحياء التطبيقي', 'Applied Biology',          '🦠', 'si-green', true,  [1],       1],
            ['الفيزياء التطبيقية',   'Applied Physics',          '🔬', 'si-blue',  true,  [1],       2],
            ['الكيمياء التطبيقية',   'Applied Chemistry',        '🧪', 'si-red',   true,  [1],       3],
            // School — engineering(2)
            ['الرياضيات التطبيقية',  'Applied Mathematics',      '🔢', 'si-blue',  true,  [2],       1],
            ['الحاسوب',              'Computer Science',         '💻', 'si-blue',  true,  [2],       2],
            ['الأحياء',              'Biology (Eng)',             '🧬', 'si-green', true,  [2],       3],
            ['علم الأرض والبيئة',    'Earth & Environment',      '🌍', 'si-green', true,  [2],       4],
            // School — business(3)
            ['الإحصاء والاحتمالات',  'Statistics & Probability', '📉', 'si-blue',  true,  [3],       1],
            ['مبادئ الإدارة',        'Principles of Management', '📣', 'si-gold',  true,  [3],       2],
            ['الحاسوب',              'Computer Science',         '💻', 'si-blue',  true,  [3],       3],
            ['اللغة الفرنسية',       'French Language',          '🇫🇷', 'si-blue', true,  [3],       4],
            // School — humanities(4)
            ['علم النفس',            'Psychology',               '🧠', 'si-blue',  true,  [4],       1],
            ['علم الاجتماع',         'Sociology',                '👥', 'si-blue',  true,  [4],       2],
            ['اللغة الفرنسية',       'French Language',          '🇫🇷', 'si-blue', true,  [4],       3],
            ['الفلسفة والمنطق',      'Philosophy & Logic',       '💭', 'si-gold',  true,  [4],       4],
        ];

        foreach ($tawjihiDefs as [$nameAr, $nameEn, $icon, $color, $elective, $streamOrders, $order]) {
            $subCatOrder = $elective ? 2 : 1; // 1=وزارية, 2=مدرسية

            foreach ($streamOrders as $streamOrder) {
                $catId = $streamSubCatIds[$streamOrder][$subCatOrder] ?? null;

                Subject::updateOrCreate(
                    ['name_ar' => $nameAr, 'category_id' => $catId],
                    [
                        'name_en'     => $nameEn,
                        'icon'        => $icon,
                        'color_class' => $color,
                        'is_elective' => $elective,
                        'order_index' => $order,
                        'is_active'   => true,
                    ]
                );
            }
        }
    }
}
