<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\SiteSetting;
use App\Models\Trip;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TripController extends Controller
{
    use ApiResponse;

    // GET /my-trips — trips this teacher is the bus companion for
    public function index(Request $request): JsonResponse
    {
        $teacher = $request->user();

        $trips = Trip::with(['bus', 'students'])
            ->where('companion_teacher_id', $teacher->id)
            ->where('is_active', true)
            ->orderBy('type')
            ->orderBy('sequence_number')
            ->get()
            ->map(fn (Trip $trip) => $this->tripCard($trip));

        $school = [
            'lat' => (float) SiteSetting::raw('school_lat'),
            'lng' => (float) SiteSetting::raw('school_lng'),
        ];

        return $this->success([
            'school' => $school,
            'trips'  => $trips,
        ]);
    }

    private function tripCard(Trip $trip): array
    {
        return [
            'id'                     => $trip->id,
            'name'                   => $trip->name,
            'type'                   => $trip->type, // pickup (morning) | dropoff (afternoon)
            'bus'                    => ['id' => $trip->bus->id, 'name' => $trip->bus->name],
            'total_distance_km'      => $trip->total_distance_km ? (float) $trip->total_distance_km : null,
            'total_duration_minutes' => $trip->total_duration_minutes,
            'google_maps_url'        => $trip->google_maps_url,
            'students'               => $trip->students->map(fn ($s) => [
                'id'          => $s->id,
                'name'        => $s->name,
                'avatar'      => $s->avatar ? asset('assets/uploads/students/' . $s->avatar) : null,
                'home_lat'    => (float) $s->home_lat,
                'home_lng'    => (float) $s->home_lng,
                'stop_order'  => $s->pivot->stop_order,
                'eta_minutes' => $s->pivot->eta_minutes,
            ])->values(),
        ];
    }
}
