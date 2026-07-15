<?php

namespace Database\Seeders;

use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Database\Seeder;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        $teachers = [
            [
                'name'           => 'أ. محمد أحمد الزيود',
                'email'          => 'mohammad.zayoud@noor.jo',
                'phone'          => '+962799001001',
                'password'       => 'noor2024',
                'gender'         => 'male',
                'total_students' => 1850,
                'is_active'      => true,
                'subject_names'  => ['الرياضيات', 'الفيزياء'],
            ],
            [
                'name'           => 'أ. سارة عبدالله النمر',
                'email'          => 'sara.namer@noor.jo',
                'phone'          => '+962799001002',
                'password'       => 'noor2024',
                'gender'         => 'female',
                'total_students' => 1420,
                'is_active'      => true,
                'subject_names'  => ['اللغة العربية', 'اللغة العربية وآدابها'],
            ],
            [
                'name'           => 'أ. خالد نادر الشريدة',
                'email'          => 'khaled.shraida@noor.jo',
                'phone'          => '+962799001003',
                'password'       => 'noor2024',
                'gender'         => 'male',
                'total_students' => 980,
                'is_active'      => true,
                'subject_names'  => ['الكيمياء'],
            ],
            [
                'name'           => 'أ. رنا هاني جرار',
                'email'          => 'rana.jarrar@noor.jo',
                'phone'          => '+962799001004',
                'password'       => 'noor2024',
                'gender'         => 'female',
                'total_students' => 1100,
                'is_active'      => true,
                'subject_names'  => ['اللغة الإنجليزية'],
            ],
        ];

        foreach ($teachers as $data) {
            $subjectNames = $data['subject_names'];
            unset($data['subject_names']);

            $teacher = Teacher::updateOrCreate(
                ['email' => $data['email']],
                array_merge($data, ['password' => bcrypt($data['password'])])
            );

            $subjectIds = Subject::whereIn('name_ar', $subjectNames)->pluck('id');
            $teacher->subjects()->sync($subjectIds);
        }
    }
}
