<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            SubjectSeeder::class,
            TeacherSeeder::class,
            ExamSeeder::class,
            ClassesSeeder::class,
            SiteSettingSeeder::class,
        ]);
    }
}
