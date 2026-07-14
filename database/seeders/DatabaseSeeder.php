<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            CategorySeeder::class,
            SubjectSeeder::class,
            TeacherSeeder::class,
            CourseSeeder::class,
            ExamSeeder::class,
            ClassesSeeder::class,
            SiteSettingSeeder::class,
        ]);
    }
}
