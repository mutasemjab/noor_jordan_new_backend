<?php

namespace Database\Seeders;

use App\Models\Bus;
use App\Models\SiteSetting;
use App\Models\Teacher;
use App\Services\TripPlannerService;
use Illuminate\Database\Seeder;

/**
 * Generates demo pickup/dropoff trips from the students+buses seeded above,
 * then assigns the existing teachers (from TeacherSeeder) as bus companions,
 * round-robin, so there's ready-made data to test the teacher "my-trips" API.
 *
 * This calls the real Google Directions API (via TripPlannerService) — it
 * needs GOOGLE_MAPS_API_KEY configured to actually produce trips. If it isn't
 * set yet, this prints a clear warning and skips instead of crashing the rest
 * of the seed run (buses/students seeded above are unaffected either way).
 */
class TripDemoSeeder extends Seeder
{
    public function run(): void
    {
        // Default East Amman school location — only if the admin hasn't set one already.
        if (! SiteSetting::raw('school_lat') || ! SiteSetting::raw('school_lng')) {
            SiteSetting::set('school_lat', '31.9650000', null, 'trips');
            SiteSetting::set('school_lng', '35.9550000', null, 'trips');
        }

        $busIds = Bus::where('is_active', true)->pluck('id')->all();

        if (empty($busIds)) {
            $this->command?->warn('لا توجد باصات — شغّل BusSeeder أولاً.');
            return;
        }

        $planner = app(TripPlannerService::class);
        $created = collect();

        foreach (['pickup', 'dropoff'] as $type) {
            try {
                $created = $created->merge($planner->generate($type, $busIds));
            } catch (\Throwable $e) {
                $this->command?->warn("تعذر توليد جولات ({$type}): {$e->getMessage()}");
            }
        }

        if ($created->isEmpty()) {
            $this->command?->warn('لم يتم إنشاء أي جولة — تأكد من ضبط GOOGLE_MAPS_API_KEY ووجود طلاب مسجّلين على الباص.');
            return;
        }

        $teachers = Teacher::where('is_active', true)->orderBy('id')->get();

        if ($teachers->isEmpty()) {
            $this->command?->warn('لا يوجد معلمون لتعيينهم كمرافقين — شغّل TeacherSeeder أولاً.');
            return;
        }

        foreach ($created->values() as $i => $trip) {
            $teacher = $teachers[$i % $teachers->count()];
            $trip->update(['companion_teacher_id' => $teacher->id]);
        }

        $this->command?->info("{$created->count()} جولة تم توليدها وتعيين مرافقين لها من {$teachers->count()} معلم.");
    }
}
