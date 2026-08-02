<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\SiteSetting;
use App\Models\Trip;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TripController extends Controller
{
    use ApiResponse;

    // GET /my-trip — the student's currently-running bus trip (if any), with the
    // bus's live position, so the app can show "الباص قريب" / a live map.
    public function show(Request $request): JsonResponse
    {
        $student = $request->user();

        $trip = Trip::with('bus')
            ->whereHas('students', fn ($q) => $q->where('student_id', $student->id))
            ->where('status', 'in_progress')
            ->first();

        if (! $trip) {
            return $this->success(['trip' => null], 'لا توجد جولة نشطة حالياً.');
        }

        $pivot = $trip->students()->where('student_id', $student->id)->first()->pivot;

        return $this->success([
            'trip' => [
                'id'               => $trip->id,
                'type'             => $trip->type,
                'bus_name'         => $trip->bus->name,
                'bus_location'     => $trip->current_lat && $trip->current_lng ? [
                    'lat'      => (float) $trip->current_lat,
                    'lng'      => (float) $trip->current_lng,
                    'updated_at' => optional($trip->last_location_at)->toIso8601String(),
                ] : null,
                'my_stop_order'    => $pivot->stop_order,
                'my_eta_minutes'   => $pivot->eta_minutes,
                'arrived_at_me'    => $pivot->arrived_at,
            ],
            'school' => [
                'lat' => (float) SiteSetting::raw('school_lat'),
                'lng' => (float) SiteSetting::raw('school_lng'),
            ],
        ]);
    }
}
