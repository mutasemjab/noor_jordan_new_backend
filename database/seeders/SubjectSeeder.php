<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\ClassSubject;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $gradeNames = [
            1  => 'الأول',
            2  => 'الثاني',
            3  => 'الثالث',
            4  => 'الرابع',
            5  => 'الخامس',
            6  => 'السادس',
            7  => 'السابع',
            8  => 'الثامن',
            9  => 'التاسع',
            10 => 'العاشر',
            11 => 'الحادي عشر',
            12 => 'الثاني عشر',
        ];

        $subjects = [

            // Grades 1-4
            [
                'name_ar' => 'اللغة العربية',
                'name_en' => 'Arabic Language',
                'icon' => '📖',
                'color_class' => 'si-blue',
                'grades' => [1,2,3,4],
                'order' => 1,
            ],
            [
                'name_ar' => 'اللغة الإنجليزية',
                'name_en' => 'English Language',
                'icon' => '🌐',
                'color_class' => 'si-cyan',
                'grades' => [1,2,3,4],
                'order' => 2,
            ],
            [
                'name_ar' => 'الرياضيات',
                'name_en' => 'Mathematics',
                'icon' => '📐',
                'color_class' => 'si-blue',
                'grades' => [1,2,3,4],
                'order' => 3,
            ],
            [
                'name_ar' => 'العلوم',
                'name_en' => 'Science',
                'icon' => '🔬',
                'color_class' => 'si-green',
                'grades' => [1,2,3],
                'order' => 4,
            ],
            [
                'name_ar' => 'العلوم العامة',
                'name_en' => 'General Science',
                'icon' => '🧪',
                'color_class' => 'si-green',
                'grades' => [4],
                'order' => 4,
            ],
            [
                'name_ar' => 'التربية الإسلامية',
                'name_en' => 'Islamic Education',
                'icon' => '☪️',
                'color_class' => 'si-gold',
                'grades' => [1,2,3,4],
                'order' => 5,
            ],
            [
                'name_ar' => 'التربية الوطنية',
                'name_en' => 'Civic Education',
                'icon' => '🏛️',
                'color_class' => 'si-green',
                'grades' => [1,2,3,4],
                'order' => 6,
            ],
            [
                'name_ar' => 'التربية الفنية',
                'name_en' => 'Art Education',
                'icon' => '🎨',
                'color_class' => 'si-red',
                'grades' => [1,2,3,4],
                'order' => 7,
            ],
            [
                'name_ar' => 'التربية البدنية',
                'name_en' => 'Physical Education',
                'icon' => '⚽',
                'color_class' => 'si-green',
                'grades' => [1,2,3,4],
                'order' => 8,
            ],

            // Grades 5-6
            [
                'name_ar' => 'اللغة العربية',
                'name_en' => 'Arabic Language',
                'icon' => '📖',
                'color_class' => 'si-blue',
                'grades' => [5,6],
                'order' => 1,
            ],
            [
                'name_ar' => 'اللغة الإنجليزية',
                'name_en' => 'English Language',
                'icon' => '🌐',
                'color_class' => 'si-cyan',
                'grades' => [5,6],
                'order' => 2,
            ],
            [
                'name_ar' => 'الرياضيات',
                'name_en' => 'Mathematics',
                'icon' => '📐',
                'color_class' => 'si-blue',
                'grades' => [5,6],
                'order' => 3,
            ],
            [
                'name_ar' => 'العلوم العامة',
                'name_en' => 'General Science',
                'icon' => '🧪',
                'color_class' => 'si-green',
                'grades' => [5,6],
                'order' => 4,
            ],
            [
                'name_ar' => 'التربية الإسلامية',
                'name_en' => 'Islamic Education',
                'icon' => '☪️',
                'color_class' => 'si-gold',
                'grades' => [5,6],
                'order' => 5,
            ],
            [
                'name_ar' => 'الدراسات الاجتماعية',
                'name_en' => 'Social Studies',
                'icon' => '🗺️',
                'color_class' => 'si-gold',
                'grades' => [5,6],
                'order' => 6,
            ],
            [
                'name_ar' => 'الحاسوب',
                'name_en' => 'Computer Science',
                'icon' => '💻',
                'color_class' => 'si-blue',
                'grades' => [5,6],
                'order' => 7,
            ],
            [
                'name_ar' => 'التربية الفنية',
                'name_en' => 'Art Education',
                'icon' => '🎨',
                'color_class' => 'si-red',
                'grades' => [5,6],
                'order' => 8,
            ],
            [
                'name_ar' => 'التربية البدنية',
                'name_en' => 'Physical Education',
                'icon' => '⚽',
                'color_class' => 'si-green',
                'grades' => [5,6],
                'order' => 9,
            ],

            // Grades 7-12
            [
                'name_ar' => 'اللغة العربية',
                'name_en' => 'Arabic Language',
                'icon' => '📖',
                'color_class' => 'si-blue',
                'grades' => [7,8,9,10,11,12],
                'order' => 1,
            ],
            [
                'name_ar' => 'اللغة الإنجليزية',
                'name_en' => 'English Language',
                'icon' => '🌐',
                'color_class' => 'si-cyan',
                'grades' => [7,8,9,10,11,12],
                'order' => 2,
            ],
            [
                'name_ar' => 'الرياضيات',
                'name_en' => 'Mathematics',
                'icon' => '📐',
                'color_class' => 'si-blue',
                'grades' => [7,8,9,10,11,12],
                'order' => 3,
            ],
            [
                'name_ar' => 'الفيزياء',
                'name_en' => 'Physics',
                'icon' => '⚛️',
                'color_class' => 'si-blue',
                'grades' => [7,8,9,10,11,12],
                'order' => 4,
            ],
            [
                'name_ar' => 'الكيمياء',
                'name_en' => 'Chemistry',
                'icon' => '⚗️',
                'color_class' => 'si-red',
                'grades' => [7,8,9,10,11,12],
                'order' => 5,
            ],
            [
                'name_ar' => 'الأحياء',
                'name_en' => 'Biology',
                'icon' => '🧬',
                'color_class' => 'si-green',
                'grades' => [7,8,9,10,11,12],
                'order' => 6,
            ],
            [
                'name_ar' => 'التربية الإسلامية',
                'name_en' => 'Islamic Education',
                'icon' => '☪️',
                'color_class' => 'si-gold',
                'grades' => [7,8,9,10,11,12],
                'order' => 7,
            ],
            [
                'name_ar' => 'التاريخ',
                'name_en' => 'History',
                'icon' => '🏛️',
                'color_class' => 'si-gold',
                'grades' => [7,8,9,10,11,12],
                'order' => 8,
            ],
            [
                'name_ar' => 'الجغرافيا',
                'name_en' => 'Geography',
                'icon' => '🌍',
                'color_class' => 'si-green',
                'grades' => [7,8,9,10,11,12],
                'order' => 9,
            ],
            [
                'name_ar' => 'الحاسوب',
                'name_en' => 'Computer Science',
                'icon' => '💻',
                'color_class' => 'si-blue',
                'grades' => [7,8,9,10,11,12],
                'order' => 10,
            ],
            [
                'name_ar' => 'التربية الوطنية',
                'name_en' => 'Civic Education',
                'icon' => '🏛️',
                'color_class' => 'si-green',
                'grades' => [10,11,12],
                'order' => 11,
            ],
        ];

        foreach ($subjects as $item) {

            $subject = Subject::updateOrCreate(
                ['name_ar' => $item['name_ar']],
                [
                    'name_en' => $item['name_en'],
                    'icon' => $item['icon'],
                    'color_class' => $item['color_class'],
                    'is_elective' => false,
                    'order_index' => $item['order'],
                    'is_active' => true,
                ]
            );

            foreach ($item['grades'] as $grade) {

                $classes = SchoolClass::where(
                    'name',
                    'like',
                    'الصف '.$gradeNames[$grade].'%'
                )->get();

                foreach ($classes as $class) {

                    ClassSubject::updateOrCreate(
                        [
                            'class_id' => $class->id,
                            'subject_id' => $subject->id,
                        ],
                        [
                            'teacher_id' => null,
                        ]
                    );
                }
            }
        }
    }
}
