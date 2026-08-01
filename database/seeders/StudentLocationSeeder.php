<?php

namespace Database\Seeders;

use App\Models\SchoolClass;
use App\Models\Student;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Seeder;

/**
 * Demo students scattered across East Amman (عمان الشرقية), for testing the
 * bus trip planner without waiting on real student registrations. Uses a
 * fixed national_id range (95000000xx) that won't collide with real records.
 */
class StudentLocationSeeder extends Seeder
{
    private const TOTAL_STUDENTS = 120;

    // Rough bounding box covering East Amman neighborhoods
    // (النزهة، ماركا، القويسمة، جبل النصر، النصر، الوحدات).
    private const LAT_MIN = 31.945;
    private const LAT_MAX = 31.985;
    private const LNG_MIN = 35.945;
    private const LNG_MAX = 35.985;

    public function run(): void
    {
        $classIds = SchoolClass::where('is_active', true)->pluck('id');

        if ($classIds->isEmpty()) {
            $this->command?->warn('لا توجد صفوف — شغّل ClassesSeeder أولاً.');
            return;
        }

        $faker = FakerFactory::create('ar_JO');

        for ($i = 1; $i <= self::TOTAL_STUDENTS; $i++) {
            $nationalId = '9500000' . str_pad((string) $i, 3, '0', STR_PAD_LEFT);
            $gender     = $faker->randomElement(['male', 'female']);

            Student::updateOrCreate(
                ['national_id' => $nationalId],
                [
                    'name'                   => $gender === 'male' ? $faker->name('male') : $faker->name('female'),
                    'password'               => 'student123',
                    'gender'                 => $gender,
                    'class_id'               => $classIds->random(),
                    'is_active'              => true,
                    'home_lat'               => round($faker->randomFloat(7, self::LAT_MIN, self::LAT_MAX), 7),
                    'home_lng'               => round($faker->randomFloat(7, self::LNG_MIN, self::LNG_MAX), 7),
                    // ~85% ride the bus each way, independently, so some walk one way and bus the other.
                    'transport_to_school'    => $faker->boolean(85) ? 'bus' : 'walk',
                    'transport_from_school'  => $faker->boolean(85) ? 'bus' : 'walk',
                ]
            );
        }

        $this->command?->info(self::TOTAL_STUDENTS . ' طالب تجريبي تم إنشاؤهم بمواقع داخل عمان الشرقية.');
    }
}
