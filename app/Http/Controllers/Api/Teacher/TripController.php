<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Admin\FCMController;
use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\SiteSetting;
use App\Models\Student;
use App\Models\StudentNotification;
use App\Models\Trip;
use App\Services\GoogleMapsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TripController extends Controller
{
    use ApiResponse;

    /** Send the "قربنا" push once the bus is within this many meters of a student's home. */
    private const NOTIFY_RADIUS_METERS = 700;

    /** Auto-mark a stop as arrived (and move on to the next one) within this radius. */
    private const ARRIVE_RADIUS_METERS = 150;

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

    // POST /trips/{trip}/start — companion begins the run; resets today's stop progress
    public function start(Request $request, Trip $trip): JsonResponse
    {
        $this->authorizeCompanion($request, $trip);

        $trip->update([
            'status'     => 'in_progress',
            'started_at' => now(),
        ]);

        // Fresh run: clear any notified/arrived flags left over from a previous day.
        $trip->students()->newPivotStatement()
            ->where('trip_id', $trip->id)
            ->update(['notified_at' => null, 'arrived_at' => null]);

        return $this->success($this->tripCard($trip->fresh(['bus', 'students'])), 'بدأت الجولة.');
    }

    // POST /trips/{trip}/location — companion's device reports its current position.
    // Checks proximity to the next un-arrived stop; sends the "قربنا" push once per
    // stop, and auto-advances to the next stop once close enough to count as arrived.
    public function location(Request $request, Trip $trip): JsonResponse
    {
        $this->authorizeCompanion($request, $trip);

        $data = $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
        ]);

        if ($trip->status !== 'in_progress') {
            return $this->error('الجولة لم تبدأ بعد. استخدم /trips/{trip}/start أولاً.', 422);
        }

        $trip->update([
            'current_lat'      => $data['lat'],
            'current_lng'      => $data['lng'],
            'last_location_at' => now(),
        ]);

        $trip->load('students');
        $next = $trip->nextStop();

        if (! $next) {
            return $this->success(['next_stop' => null, 'trip_status' => $trip->status], 'كل الطلاب تم الوصول إليهم.');
        }

        $distance = GoogleMapsService::distanceMeters(
            (float) $data['lat'], (float) $data['lng'],
            (float) $next->home_lat, (float) $next->home_lng
        );

        $justNotified = false;
        $justArrived  = false;

        if ($distance <= self::ARRIVE_RADIUS_METERS) {
            $this->markStop($trip, $next->id, ['arrived_at' => now()]);
            $justArrived = true;

            // Move on immediately so the response reflects the *new* next stop.
            $trip->load('students');
            $next = $trip->nextStop();
            if ($next) {
                $distance = GoogleMapsService::distanceMeters(
                    (float) $data['lat'], (float) $data['lng'],
                    (float) $next->home_lat, (float) $next->home_lng
                );
            }
        } elseif ($distance <= self::NOTIFY_RADIUS_METERS && ! $next->pivot->notified_at) {
            $this->notifyStudentApproaching($trip, $next);
            $this->markStop($trip, $next->id, ['notified_at' => now()]);
            $justNotified = true;
        }

        return $this->success([
            'trip_status' => $trip->status,
            'next_stop'   => $next ? [
                'student_id'      => $next->id,
                'name'            => $next->name,
                'distance_meters' => (int) round($distance),
                'notified'        => $justNotified,
                'arrived'         => $justArrived,
            ] : null,
        ]);
    }

    // POST /trips/{trip}/students/{student}/arrived — manual override (GPS drift,
    // student boarded/exited outside the auto-detection radius, etc.)
    public function markArrived(Request $request, Trip $trip, Student $student): JsonResponse
    {
        $this->authorizeCompanion($request, $trip);
        $this->markStop($trip, $student->id, ['arrived_at' => now()]);

        return $this->success(null, 'تم تحديث حالة الطالب.');
    }

    // POST /trips/{trip}/complete
    public function complete(Request $request, Trip $trip): JsonResponse
    {
        $this->authorizeCompanion($request, $trip);

        $trip->update(['status' => 'completed', 'completed_at' => now()]);

        return $this->success(null, 'تم إنهاء الجولة.');
    }

    private function authorizeCompanion(Request $request, Trip $trip): void
    {
        if ($trip->companion_teacher_id !== $request->user()->id) {
            throw ValidationException::withMessages(['trip' => 'هذه الجولة ليست ضمن جولاتك.']);
        }
    }

    private function markStop(Trip $trip, int $studentId, array $pivotData): void
    {
        $trip->students()->updateExistingPivot($studentId, $pivotData);
    }

    private function notifyStudentApproaching(Trip $trip, Student $student): void
    {
        $title = 'الباص قريب منك 🚌';
        $body  = 'دقيقة وبنكون عندك، جهز حالك!';

        StudentNotification::create([
            'student_id' => $student->id,
            'title'      => $title,
            'body'       => $body,
            'type'       => 'bus_approaching',
            'data'       => ['trip_id' => $trip->id, 'bus_id' => $trip->bus_id],
        ]);

        if ($student->fcm_token) {
            FCMController::sendToToken($title, $body, $student->fcm_token, 'bus_tracking');
        }
    }

    private function tripCard(Trip $trip): array
    {
        return [
            'id'                     => $trip->id,
            'name'                   => $trip->name,
            'type'                   => $trip->type, // pickup (morning) | dropoff (afternoon)
            'status'                 => $trip->status,
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
                'arrived_at'  => $s->pivot->arrived_at,
            ])->values(),
        ];
    }
}
