@extends('admin.layouts.app')
@section('title', 'الجدول الدراسي — ' . $class->name)

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-title">الجدول الدراسي</h1>
        <p class="page-sub">{{ $class->name }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.period-settings.index') }}" class="btn-outline-sm">
            <i class="bi bi-clock"></i> إعدادات الحصص
        </a>
        <a href="{{ route('admin.classes.show', $class->id) }}" class="btn-outline-sm">
            <i class="bi bi-arrow-left"></i> رجوع
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

@if($periods->isEmpty())
<div class="panel-card">
    <div class="panel-card-body text-center py-5">
        <i class="bi bi-clock" style="font-size:3rem;color:var(--muted)"></i>
        <p class="mt-3" style="color:var(--muted)">لم تُضف أي حصص بعد.
            <a href="{{ route('admin.period-settings.index') }}">أضف الحصص أولاً</a>
        </p>
    </div>
</div>
@elseif($classSubjects->isEmpty())
<div class="panel-card">
    <div class="panel-card-body text-center py-5">
        <i class="bi bi-journal" style="font-size:3rem;color:var(--muted)"></i>
        <p class="mt-3" style="color:var(--muted)">لم تُضف أي مواد لهذا الصف بعد.
            <a href="{{ route('admin.classes.show', $class->id) }}">أضف المواد أولاً</a>
        </p>
    </div>
</div>
@else

@php
    $days = [0 => 'الأحد', 1 => 'الاثنين', 2 => 'الثلاثاء', 3 => 'الأربعاء', 4 => 'الخميس'];
@endphp

<form action="{{ route('admin.classes.schedule.update', $class->id) }}" method="POST">
@csrf

<div class="panel-card">
    <div class="panel-card-body p-0" style="overflow-x:auto">
        <table class="data-table" style="min-width:700px">
            <thead>
                <tr>
                    <th style="min-width:130px">الحصة</th>
                    @foreach($days as $d => $name)
                    <th>{{ $name }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($periods as $period)
                <tr>
                    <td>
                        <div style="font-weight:600;font-size:.88rem">{{ $period->label }}</div>
                        <div style="font-size:.72rem;color:var(--muted)">
                            {{ \Carbon\Carbon::parse($period->start_time)->format('H:i') }}
                            —
                            {{ \Carbon\Carbon::parse($period->end_time)->format('H:i') }}
                        </div>
                    </td>
                    @foreach($days as $d => $name)
                    <td style="padding:6px">
                        <select name="schedule[{{ $d }}][{{ $period->period_number }}]"
                                class="form-select form-select-sm" style="font-size:.8rem">
                            <option value="">—</option>
                            @foreach($classSubjects as $cs)
                            <option value="{{ $cs->subject_id }}"
                                @selected(($current[$d][$period->period_number] ?? null) == $cs->subject_id)>
                                {{ $cs->subject->name_ar }}
                            </option>
                            @endforeach
                        </select>
                    </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="panel-card-body border-top">
        <button type="submit" class="btn-primary-sm">
            <i class="bi bi-save"></i> حفظ الجدول
        </button>
    </div>
</div>

</form>
@endif

@endsection
