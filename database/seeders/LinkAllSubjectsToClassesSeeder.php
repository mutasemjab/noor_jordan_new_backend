<?php

namespace Database\Seeders;

use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LinkAllSubjectsToClassesSeeder extends Seeder
{
    public function run(): void
    {
        $classIds   = SchoolClass::pluck('id');
        $subjectIds = Subject::pluck('id');

        $now  = now();
        $rows = [];

        foreach ($subjectIds as $subjectId) {
            foreach ($classIds as $classId) {
                $rows[] = [
                    'class_id'   => $classId,
                    'subject_id' => $subjectId,
                    'teacher_id' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('class_subjects')->insertOrIgnore($chunk);
        }
    }
}
