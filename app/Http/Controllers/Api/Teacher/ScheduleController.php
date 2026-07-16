<?php

namespace App\Http\Controllers\Api\Teacher;

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
        $teacher = $request->user();

        $periods = PeriodSetting::orderBy('period_number')->get();

        $schedules = ClassSchedule::where('teacher_id', $teacher->id)
            ->with(['schoolClass:id,name', 'subject:id,name_ar,name_en,color_class'])
            ->orderBy('day')
            ->orderBy('period_number')
            ->get();

        $days = ClassSchedule::$dayNames;

        $grid = [];
        foreach ($days as $dayNum => $dayName) {
            $grid[$dayNum] = [
                'day'     => $dayNum,
                'name'    => $dayName,
                'periods' => [],
            ];
            foreach ($periods as $p) {
                $slot = $schedules->first(
                    fn ($s) => $s->day === $dayNum && $s->period_number === $p->period_number
                );
                $grid[$dayNum]['periods'][] = [
                    'period_number' => $p->period_number,
                    'label'         => $p->label,
                    'start_time'    => \Carbon\Carbon::parse($p->start_time)->format('H:i'),
                    'end_time'      => \Carbon\Carbon::parse($p->end_time)->format('H:i'),
                    'class'         => $slot ? ['id' => $slot->schoolClass->id, 'name' => $slot->schoolClass->name] : null,
                    'subject'       => $slot ? [
                        'id'      => $slot->subject->id,
                        'name'    => $slot->subject->name_ar,
                        'color'   => $slot->subject->color_class,
                    ] : null,
                ];
            }
        }

        return $this->success(array_values($grid));
    }
}
