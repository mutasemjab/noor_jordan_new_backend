<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassesSeeder extends Seeder
{
    public function run(): void
    {
        $grades = [
            'الصف الأول'   => 1,
            'الصف الثاني'  => 2,
            'الصف الثالث'  => 3,
            'الصف الرابع'  => 4,
            'الصف الخامس'  => 5,
            'الصف السادس'  => 6,
            'الصف السابع'  => 7,
            'الصف الثامن'  => 8,
            'الصف التاسع'  => 9,
            'الصف العاشر'  => 10,
            'الصف الحادي عشر'  => 11,
            'الصف الثاني عشر'  => 12,
        ];

        $sections = ['أ', 'ب', 'ج'];

        $rows = [];
        $now  = now();

        foreach ($grades as $gradeName => $gradeNum) {
            foreach ($sections as $section) {
                $rows[] = [
                    'name'       => $gradeName . ' - شعبة ' . $section,
                    'is_active'  => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        DB::table('classes')->insert($rows);
    }
}
