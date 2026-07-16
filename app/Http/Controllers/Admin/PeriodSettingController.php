<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PeriodSetting;
use Illuminate\Http\Request;

class PeriodSettingController extends Controller
{
    public function index()
    {
        $periods = PeriodSetting::orderBy('period_number')->get();
        return view('admin.period_settings.index', compact('periods'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'label'      => 'required|string|max:50',
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i|after:start_time',
        ]);

        $next = (PeriodSetting::max('period_number') ?? 0) + 1;
        PeriodSetting::create(array_merge($data, ['period_number' => $next]));

        return back()->with('success', 'تم إضافة الحصة ' . $next . ' بنجاح.');
    }

    public function update(Request $request, PeriodSetting $periodSetting)
    {
        $data = $request->validate([
            'label'      => 'required|string|max:50',
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i|after:start_time',
        ]);

        $periodSetting->update($data);
        return back()->with('success', 'تم تحديث الحصة.');
    }

    public function destroy(PeriodSetting $periodSetting)
    {
        $num = $periodSetting->period_number;
        $periodSetting->delete();

        // Re-number remaining periods
        PeriodSetting::where('period_number', '>', $num)
            ->orderBy('period_number')
            ->each(fn ($p) => $p->decrement('period_number'));

        return back()->with('success', 'تم حذف الحصة وإعادة الترتيب.');
    }
}
