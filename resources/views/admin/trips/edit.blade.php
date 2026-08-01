@extends('admin.layouts.app')
@section('title', 'تعديل جولة')

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-title">{{ $trip->name }}</h1>
        <p class="page-sub">
            {{ $trip->bus->name }} —
            {{ $trip->type === 'pickup' ? 'صباحاً (ذهاب للمدرسة)' : 'عصراً (إياب للمنزل)' }}
            @if($trip->total_distance_km)
                — {{ $trip->total_distance_km }} كم / {{ $trip->total_duration_minutes }} دقيقة
            @endif
        </p>
    </div>
    <div class="d-flex gap-2">
        @if($trip->google_maps_url)
        <a href="{{ $trip->google_maps_url }}" target="_blank" class="btn-outline-sm">
            <i class="bi bi-map"></i> عرض المسار على الخريطة
        </a>
        @endif
        <a href="{{ route('admin.trips.index') }}" class="btn-outline-sm"><i class="bi bi-arrow-left"></i> رجوع</a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="row g-3">
    <div class="col-12 col-xl-4">
        <div class="panel-card">
            <div class="panel-card-header"><h2 class="panel-card-title">بيانات الجولة</h2></div>
            <div class="panel-card-body">
                <form action="{{ route('admin.trips.update', $trip->id) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">اسم الجولة</label>
                        <input type="text" name="name" value="{{ old('name', $trip->name) }}" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">المعلم المرافق</label>
                        <select name="companion_teacher_id" class="form-select select2">
                            <option value="">— بدون مرافق —</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" @selected(old('companion_teacher_id', $trip->companion_teacher_id) == $teacher->id)>
                                    {{ $teacher->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn-primary-sm w-100 justify-content-center">
                        <i class="bi bi-save"></i> حفظ
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-8">
        <div class="panel-card">
            <div class="panel-card-header">
                <h2 class="panel-card-title">طلاب الجولة ({{ $trip->students->count() }})</h2>
            </div>
            <div class="panel-card-body p-0">
                <form action="{{ route('admin.trips.order', $trip->id) }}" method="POST">
                    @csrf @method('PUT')
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>الترتيب</th>
                                <th>الطالب</th>
                                <th>الوقت التقديري</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($trip->students as $student)
                            <tr>
                                <td style="width:90px">
                                    <input type="hidden" name="order[{{ $loop->index }}][student_id]" value="{{ $student->id }}">
                                    <input type="number" name="order[{{ $loop->index }}][stop_order]"
                                           value="{{ $student->pivot->stop_order }}" min="1"
                                           class="form-control form-control-sm">
                                </td>
                                <td style="font-weight:500">{{ $student->name }}</td>
                                <td style="font-size:.82rem;color:var(--muted)">
                                    {{ $student->pivot->eta_minutes ? $student->pivot->eta_minutes . ' د' : '—' }}
                                </td>
                                <td style="width:50px"></td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center py-4" style="color:var(--muted)">لا يوجد طلاب بهذه الجولة.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    @if($trip->students->isNotEmpty())
                    <div class="p-3 border-top">
                        <button type="submit" class="btn-primary-sm"><i class="bi bi-save"></i> حفظ الترتيب</button>
                    </div>
                    @endif
                </form>

                @if($trip->students->isNotEmpty())
                <div class="p-3 border-top">
                    <div class="fw-semibold mb-2" style="font-size:.85rem">إزالة طالب من الجولة</div>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($trip->students as $student)
                        <form action="{{ route('admin.trips.students.remove', [$trip->id, $student->id]) }}" method="POST"
                              onsubmit="return confirm('إزالة {{ $student->name }} من الجولة؟')">
                            @csrf @method('DELETE')
                            <button class="btn-outline-sm" style="padding:3px 10px;font-size:.78rem;color:#dc2626;border-color:#fecaca">
                                <i class="bi bi-x-circle"></i> {{ $student->name }}
                            </button>
                        </form>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
