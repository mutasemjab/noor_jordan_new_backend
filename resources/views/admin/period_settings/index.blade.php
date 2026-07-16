@extends('admin.layouts.app')
@section('title', 'إعدادات الحصص الدراسية')

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-title">إعدادات الحصص الدراسية</h1>
        <p class="page-sub">تحديد عدد الحصص وتوقيت كل حصة — يُطبَّق على جميع الصفوف</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="row g-3">

    {{-- Existing periods --}}
    <div class="col-12 col-xl-8">
        <div class="panel-card">
            <div class="panel-card-header"><h2 class="panel-card-title">الحصص الحالية ({{ $periods->count() }})</h2></div>
            <div class="panel-card-body p-0">
                <table class="data-table">
                    <thead>
                        <tr><th>رقم الحصة</th><th>الاسم</th><th>من</th><th>إلى</th><th>المدة</th><th></th></tr>
                    </thead>
                    <tbody>
                        @forelse($periods as $period)
                        <tr>
                            <td><span class="pill pill-neutral">{{ $period->period_number }}</span></td>
                            <td style="font-weight:600">{{ $period->label }}</td>
                            <td style="font-family:monospace">{{ \Carbon\Carbon::parse($period->start_time)->format('H:i') }}</td>
                            <td style="font-family:monospace">{{ \Carbon\Carbon::parse($period->end_time)->format('H:i') }}</td>
                            <td style="color:var(--muted);font-size:.82rem">
                                @php
                                    $start = \Carbon\Carbon::parse($period->start_time);
                                    $end   = \Carbon\Carbon::parse($period->end_time);
                                    $mins  = $start->diffInMinutes($end);
                                @endphp
                                {{ $mins }} دقيقة
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <button type="button"
                                            class="btn-outline-sm"
                                            style="padding:4px 8px"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#edit-{{ $period->id }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('admin.period-settings.destroy', $period->id) }}"
                                          method="POST" onsubmit="return confirm('حذف هذه الحصة؟')">
                                        @csrf @method('DELETE')
                                        <button class="btn-outline-sm" style="padding:4px 8px;color:#dc2626;border-color:#fecaca">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                                {{-- Inline edit --}}
                                <div class="collapse mt-2" id="edit-{{ $period->id }}">
                                    <form action="{{ route('admin.period-settings.update', $period->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="row g-2">
                                            <div class="col-5">
                                                <input type="text" name="label" value="{{ $period->label }}" class="form-control form-control-sm" placeholder="الاسم" required>
                                            </div>
                                            <div class="col-3">
                                                <input type="time" name="start_time" value="{{ \Carbon\Carbon::parse($period->start_time)->format('H:i') }}" class="form-control form-control-sm" required>
                                            </div>
                                            <div class="col-3">
                                                <input type="time" name="end_time" value="{{ \Carbon\Carbon::parse($period->end_time)->format('H:i') }}" class="form-control form-control-sm" required>
                                            </div>
                                            <div class="col-1">
                                                <button type="submit" class="btn-primary-sm" style="padding:4px 8px"><i class="bi bi-check-lg"></i></button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-4" style="color:var(--muted)">لم تُضف أي حصص بعد.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Add new period --}}
    <div class="col-12 col-xl-4">
        <div class="panel-card">
            <div class="panel-card-header"><h2 class="panel-card-title">إضافة حصة جديدة</h2></div>
            <div class="panel-card-body">
                <form action="{{ route('admin.period-settings.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">اسم الحصة <span class="text-danger">*</span></label>
                        <input type="text" name="label" class="form-control @error('label') is-invalid @enderror"
                               placeholder="مثال: الحصة الأولى" required>
                        @error('label')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label">من <span class="text-danger">*</span></label>
                            <input type="time" name="start_time" class="form-control @error('start_time') is-invalid @enderror" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">إلى <span class="text-danger">*</span></label>
                            <input type="time" name="end_time" class="form-control @error('end_time') is-invalid @enderror" required>
                        </div>
                    </div>
                    <p class="text-muted" style="font-size:.78rem">
                        ستُضاف هذه الحصة تلقائياً برقم {{ ($periods->count() + 1) }}.
                    </p>
                    <button type="submit" class="btn-primary-sm w-100 justify-content-center">
                        <i class="bi bi-plus-circle"></i> إضافة الحصة
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
