<?php

namespace App\Services;

use App\Models\Bus;
use App\Models\SiteSetting;
use App\Models\Student;
use App\Models\Trip;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Groups bus-riding students into capacity-constrained trips using a sweep
 * algorithm (sort by compass bearing from the school, fill each bus in that
 * order — geographically adjacent students end up on the same trip), then
 * asks GoogleMapsService to order each trip's stops for the fastest route.
 *
 * This is a heuristic, not a mathematically optimal solver (grouping students
 * by location under a capacity limit is NP-hard in general) — it's meant to
 * produce a strong starting point for the admin to review and adjust.
 */
class TripPlannerService
{
    /**
     * @param  string  $type  'pickup' (morning, to school) or 'dropoff' (afternoon, from school)
     * @param  int[]  $busIds  buses to distribute students across, in the order given
     * @return Collection<int, Trip>
     */
    public function generate(string $type, array $busIds): Collection
    {
        if (! in_array($type, ['pickup', 'dropoff'], true)) {
            throw new \InvalidArgumentException('type must be pickup or dropoff.');
        }

        $buses = Bus::whereIn('id', $busIds)->where('is_active', true)->get()->keyBy('id');
        $orderedBuses = collect($busIds)->map(fn ($id) => $buses->get($id))->filter()->values();

        if ($orderedBuses->isEmpty()) {
            throw new \RuntimeException('لم يتم اختيار أي باص فعّال.');
        }

        $schoolLat = (float) SiteSetting::raw('school_lat');
        $schoolLng = (float) SiteSetting::raw('school_lng');

        if (! $schoolLat || ! $schoolLng) {
            throw new \RuntimeException('يرجى ضبط موقع المدرسة أولاً قبل توليد الجولات.');
        }

        $field = $type === 'pickup' ? 'transport_to_school' : 'transport_from_school';

        $students = Student::where($field, 'bus')
            ->where('is_active', true)
            ->whereNotNull('home_lat')
            ->whereNotNull('home_lng')
            ->get();

        if ($students->isEmpty()) {
            return collect();
        }

        $sorted = $students
            ->map(function (Student $s) use ($schoolLat, $schoolLng) {
                $s->setAttribute('bearing', $this->bearing($schoolLat, $schoolLng, (float) $s->home_lat, (float) $s->home_lng));
                return $s;
            })
            ->sortBy('bearing')
            ->values();

        $groups = $this->groupByCapacity($sorted, $orderedBuses);

        $school = ['lat' => $schoolLat, 'lng' => $schoolLng];

        return DB::transaction(function () use ($groups, $type, $school) {
            $created = collect();

            foreach ($groups as $group) {
                $created->push($this->createTrip($type, $school, $group['bus'], $group['sequence'], $group['students']));
            }

            return $created;
        });
    }

    /**
     * Round-robins groups of up to each bus's capacity across the given buses,
     * in sweep (bearing) order. A bus is reused (sequence_number++) once every
     * bus has taken a turn and students remain.
     *
     * @return array<int, array{bus: Bus, sequence: int, students: Collection}>
     */
    private function groupByCapacity(Collection $sortedStudents, Collection $orderedBuses): array
    {
        $sequenceForBus = $orderedBuses->mapWithKeys(fn (Bus $b) => [$b->id => 1]);
        $busIndex        = 0;
        $currentBus      = $orderedBuses[$busIndex];
        $currentGroup    = collect();
        $groups          = [];

        foreach ($sortedStudents as $student) {
            if ($currentGroup->count() >= $currentBus->capacity) {
                $groups[] = ['bus' => $currentBus, 'sequence' => $sequenceForBus[$currentBus->id], 'students' => $currentGroup];
                $sequenceForBus[$currentBus->id]++;

                $currentGroup = collect();
                $busIndex     = ($busIndex + 1) % $orderedBuses->count();
                $currentBus   = $orderedBuses[$busIndex];
            }

            $currentGroup->push($student);
        }

        if ($currentGroup->isNotEmpty()) {
            $groups[] = ['bus' => $currentBus, 'sequence' => $sequenceForBus[$currentBus->id], 'students' => $currentGroup];
        }

        return $groups;
    }

    private function createTrip(string $type, array $school, Bus $bus, int $sequence, Collection $students): Trip
    {
        $waypoints = $students->map(fn (Student $s) => ['lat' => (float) $s->home_lat, 'lng' => (float) $s->home_lng])->all();

        // Pickup: homes -> school. Dropoff: school -> homes.
        $origin      = $type === 'pickup' ? $waypoints[0] : $school;
        $destination = $type === 'pickup' ? $school : $waypoints[count($waypoints) - 1];
        $middleStops = $type === 'pickup' ? array_slice($waypoints, 1) : array_slice($waypoints, 0, -1);

        // With only 1-2 students there may be no intermediate waypoints to optimize.
        $optimized = count($middleStops) > 0
            ? GoogleMapsService::optimizeRoute($origin, $destination, $middleStops)
            : ['waypoint_order' => [], 'total_distance_km' => null, 'total_duration_minutes' => null, 'etas_seconds' => [], 'google_maps_url' => null];

        $trip = Trip::create([
            'bus_id'                 => $bus->id,
            'type'                   => $type,
            'sequence_number'        => $sequence,
            'name'                   => $bus->name . ' — ' . ($type === 'pickup' ? 'جولة صباحية' : 'جولة عصرية') . ' ' . $sequence,
            'total_distance_km'      => $optimized['total_distance_km'],
            'total_duration_minutes' => $optimized['total_duration_minutes'],
            'google_maps_url'        => $optimized['google_maps_url'],
        ]);

        // Rebuild the full student order: the first/last student (used directly
        // as origin/destination above) plus the optimized middle stops.
        $orderedStudents = $this->reassembleOrder($type, $students, $optimized['waypoint_order']);

        $pivotData = [];
        foreach ($orderedStudents as $i => $student) {
            $pivotData[$student->id] = [
                'stop_order'  => $i + 1,
                'eta_minutes' => isset($optimized['etas_seconds'][$i]) ? (int) ceil($optimized['etas_seconds'][$i] / 60) : null,
            ];
        }
        $trip->students()->sync($pivotData);

        return $trip;
    }

    /**
     * @param  int[]  $waypointOrder  indices into the "middle stops" slice (see createTrip)
     */
    private function reassembleOrder(string $type, Collection $students, array $waypointOrder): Collection
    {
        $list = $students->values();

        if (empty($waypointOrder)) {
            return $list;
        }

        if ($type === 'pickup') {
            $first  = $list->first();
            $middle = $list->slice(1)->values();
            $ordered = collect($waypointOrder)->map(fn (int $i) => $middle[$i]);
            return collect([$first])->merge($ordered);
        }

        $last   = $list->last();
        $middle = $list->slice(0, -1)->values();
        $ordered = collect($waypointOrder)->map(fn (int $i) => $middle[$i]);
        return $ordered->push($last);
    }

    private function bearing(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $lat1r = deg2rad($lat1);
        $lat2r = deg2rad($lat2);
        $dLng  = deg2rad($lng2 - $lng1);

        $y = sin($dLng) * cos($lat2r);
        $x = cos($lat1r) * sin($lat2r) - sin($lat1r) * cos($lat2r) * cos($dLng);

        return fmod(rad2deg(atan2($y, $x)) + 360, 360);
    }
}
