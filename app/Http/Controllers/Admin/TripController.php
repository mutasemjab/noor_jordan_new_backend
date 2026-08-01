<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bus;
use App\Models\SiteSetting;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Trip;
use App\Services\TripPlannerService;
use Illuminate\Http\Request;

class TripController extends Controller
{
    public function __construct(private TripPlannerService $planner) {}

    public function index(Request $request)
    {
        $trips = Trip::with(['bus', 'companionTeacher'])
            ->withCount('students')
            ->when($request->type, fn ($q, $t) => $q->where('type', $t))
            ->when($request->bus_id, fn ($q, $b) => $q->where('bus_id', $b))
            ->orderBy('type')
            ->orderBy('bus_id')
            ->orderBy('sequence_number')
            ->get();

        $buses    = Bus::where('is_active', true)->orderBy('name')->get();
        $schoolLat = SiteSetting::raw('school_lat');
        $schoolLng = SiteSetting::raw('school_lng');

        $ridersCount = [
            'pickup'  => Student::where('transport_to_school', 'bus')->where('is_active', true)->count(),
            'dropoff' => Student::where('transport_from_school', 'bus')->where('is_active', true)->count(),
        ];

        return view('admin.trips.index', compact('trips', 'buses', 'schoolLat', 'schoolLng', 'ridersCount'));
    }

    public function generate(Request $request)
    {
        $data = $request->validate([
            'type'       => 'required|in:pickup,dropoff',
            'bus_ids'    => 'required|array|min:1',
            'bus_ids.*'  => 'exists:buses,id',
        ]);

        try {
            $trips = $this->planner->generate($data['type'], $data['bus_ids']);
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        if ($trips->isEmpty()) {
            return back()->with('error', 'لا يوجد طلاب مسجّلين على الباص لهذا الاتجاه.');
        }

        return redirect()->route('admin.trips.index')
            ->with('success', "تم توليد {$trips->count()} جولة بنجاح. راجعها وعدّل ما يلزم.");
    }

    public function edit(Trip $trip)
    {
        $trip->load(['bus', 'companionTeacher', 'students']);
        $teachers = Teacher::where('is_active', true)->orderBy('name')->get();

        return view('admin.trips.edit', compact('trip', 'teachers'));
    }

    public function update(Request $request, Trip $trip)
    {
        $data = $request->validate([
            'companion_teacher_id' => 'nullable|exists:teachers,id',
            'name'                 => 'nullable|string|max:255',
        ]);

        $trip->update($data);

        return redirect()->route('admin.trips.edit', $trip->id)
            ->with('success', 'تم تحديث بيانات الجولة.');
    }

    public function updateOrder(Request $request, Trip $trip)
    {
        $data = $request->validate([
            'order' => 'required|array',
            'order.*.student_id' => 'required|exists:students,id',
            'order.*.stop_order' => 'required|integer|min:1',
        ]);

        foreach ($data['order'] as $row) {
            $trip->students()->updateExistingPivot($row['student_id'], ['stop_order' => $row['stop_order']]);
        }

        return redirect()->route('admin.trips.edit', $trip->id)
            ->with('success', 'تم تحديث ترتيب الطلاب.');
    }

    public function removeStudent(Trip $trip, Student $student)
    {
        $trip->students()->detach($student->id);

        return redirect()->route('admin.trips.edit', $trip->id)
            ->with('success', 'تم إزالة الطالب من الجولة.');
    }

    public function destroy(Trip $trip)
    {
        $trip->delete();

        return redirect()->route('admin.trips.index')
            ->with('success', 'تم حذف الجولة.');
    }

    public function updateSchoolLocation(Request $request)
    {
        $data = $request->validate([
            'school_lat' => 'required|numeric|between:-90,90',
            'school_lng' => 'required|numeric|between:-180,180',
        ]);

        SiteSetting::set('school_lat', (string) $data['school_lat'], null, 'trips');
        SiteSetting::set('school_lng', (string) $data['school_lng'], null, 'trips');

        return redirect()->route('admin.trips.index')
            ->with('success', 'تم حفظ موقع المدرسة.');
    }
}
