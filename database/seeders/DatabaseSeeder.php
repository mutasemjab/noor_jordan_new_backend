<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            ClassesSeeder::class,
            TeacherSeeder::class,
            SubjectSeeder::class,
            ExamSeeder::class,
            SiteSettingSeeder::class,


        ]);
    }
}
