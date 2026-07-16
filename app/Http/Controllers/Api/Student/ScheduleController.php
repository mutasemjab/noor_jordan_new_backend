<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\ClassSchedule;
use App\Models\PeriodSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $student = $request->user();

        if (! $student->class_id) {
            return $this->error('الطالب غير مسجل في أي صف.', 404);
        }

        $periods = PeriodSetting::orderBy('period_number')->get();

        $schedules = ClassSchedule::where('class_id', $student->class_id)
            ->with(['subject:id,name_ar,name_en,icon,color_class', 'teacher:id,name,avatar'])
            ->get();

        $days = ClassSchedule::$dayNames;

        $grid = [];
        foreach ($days as $dayNum => $dayName) {
            $periods_data = [];
            foreach ($periods as $p) {
                $slot = $schedules->first(
                    fn ($s) => $s->day === $dayNum && $s->period_number === $p->period_number
                );
                $periods_data[] = [
                    'period_number' => $p->period_number,
                    'label'         => $p->label,
                    'start_time'    => \Carbon\Carbon::parse($p->start_time)->format('H:i'),
                    'end_time'      => \Carbon\Carbon::parse($p->end_time)->format('H:i'),
                    'subject'       => $slot?->subject ? [
                        'id'          => $slot->subject->id,
                        'name'        => $slot->subject->name_ar,
                        'icon'        => $slot->subject->icon,
                        'color_class' => $slot->subject->color_class,
                    ] : null,
                    'teacher'       => $slot?->teacher ? [
                        'id'     => $slot->teacher->id,
                        'name'   => $slot->teacher->name,
                        'avatar' => $slot->teacher->avatar
                            ? asset('assets/uploads/teachers/' . $slot->teacher->avatar)
                            : null,
                    ] : null,
                ];
            }
            $grid[] = ['day' => $dayNum, 'name' => $dayName, 'periods' => $periods_data];
        }

        return $this->success($grid);
    }
}
