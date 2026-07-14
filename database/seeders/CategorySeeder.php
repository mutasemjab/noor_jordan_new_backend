<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // ── Root: الصفوف الأساسية ─────────────────────────────────────────

        $primary = Category::updateOrCreate(
            ['parent_id' => null, 'name_en' => 'Basic Grades'],
            [
                'level'       => 0,
                'name_ar'     => 'الصفوف الأساسية',
                'icon'        => 'bi-backpack2',
                'order_index' => 1,
                'is_active'   => true,
            ]
        );

        $gradeNames = [
            1  => ['الأول',  'Grade 1'],
            2  => ['الثاني', 'Grade 2'],
            3  => ['الثالث', 'Grade 3'],
            4  => ['الرابع', 'Grade 4'],
            5  => ['الخامس', 'Grade 5'],
            6  => ['السادس', 'Grade 6'],
            7  => ['السابع', 'Grade 7'],
            8  => ['الثامن', 'Grade 8'],
            9  => ['التاسع', 'Grade 9'],
            10 => ['العاشر', 'Grade 10'],
        ];

        foreach ($gradeNames as $n => [$arOrd, $enLabel]) {
            $grade = Category::updateOrCreate(
                ['parent_id' => $primary->id, 'name_en' => $enLabel],
                [
                    'level'       => 1,
                    'name_ar'     => "الصف {$arOrd}",
                    'icon'        => 'bi-person-workspace',
                    'order_index' => $n,  // n = 1..10, used by overlay JS
                    'is_active'   => true,
                ]
            );

            Category::updateOrCreate(
                ['parent_id' => $grade->id, 'name_en' => 'Semester 1'],
                [
                    'level'       => 2,
                    'name_ar'     => 'الفصل الأول',
                    'icon'        => 'bi-1-circle',
                    'order_index' => 1,
                    'is_active'   => true,
                ]
            );

            Category::updateOrCreate(
                ['parent_id' => $grade->id, 'name_en' => 'Semester 2'],
                [
                    'level'       => 2,
                    'name_ar'     => 'الفصل الثاني',
                    'icon'        => 'bi-2-circle',
                    'order_index' => 2,
                    'is_active'   => true,
                ]
            );
        }

        // ── Root: التوجيهي ───────────────────────────────────────────────
        // Sub-level under each stream: مواد وزارية / مواد مدرسية

        $tawjihi = Category::updateOrCreate(
            ['parent_id' => null, 'name_en' => 'Tawjihi'],
            [
                'level'       => 0,
                'name_ar'     => 'التوجيهي',
                'icon'        => 'bi-mortarboard',
                'order_index' => 2,
                'is_active'   => true,
            ]
        );

        $streams = [
            ['الفرع الصحي',                      'Health Track',            1],
            ['فرع الهندسة والعلوم والتكنولوجيا',  'Engineering & Science',   2],
            ['فرع إدارة الأعمال',                 'Business Administration', 3],
            ['فرع الآداب والعلوم الإنسانية',      'Arts & Humanities',       4],
        ];

        foreach ($streams as [$arName, $enName, $order]) {
            $stream = Category::updateOrCreate(
                ['parent_id' => $tawjihi->id, 'name_en' => $enName],
                [
                    'level'       => 1,
                    'name_ar'     => $arName,
                    'icon'        => 'bi-award',
                    'order_index' => $order,
                    'is_active'   => true,
                ]
            );

            Category::updateOrCreate(
                ['parent_id' => $stream->id, 'name_en' => 'Ministry Subjects'],
                [
                    'level'       => 2,
                    'name_ar'     => 'مواد وزارية',
                    'icon'        => 'bi-bank',
                    'order_index' => 1,
                    'is_active'   => true,
                ]
            );

            Category::updateOrCreate(
                ['parent_id' => $stream->id, 'name_en' => 'School Subjects'],
                [
                    'level'       => 2,
                    'name_ar'     => 'مواد مدرسية',
                    'icon'        => 'bi-building',
                    'order_index' => 2,
                    'is_active'   => true,
                ]
            );
        }
    }
}
