@extends('admin.layouts.app')
@section('title', 'تسجيل الغياب والحضور')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">الغياب والحضور</h1>
        <p class="page-sub">اختر الصف والتاريخ لتسجيل حضور الطلاب</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

{{-- Filter form --}}
<div class="panel-card mb-3">
    <div class="panel-card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">الصف <span class="text-danger">*</span></label>
                <select name="class_id" class="form-select select2" required>
                    <option value="">— اختر الصف —</option>
                    @foreach($classes as $class)
                    <option value="{{ $class->id }}" @selected(request('class_id') == $class->id)>{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">التاريخ <span class="text-danger">*</span></label>
                <input type="date" name="date" value="{{ request('date', date('Y-m-d')) }}" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">الحصة (اختياري)</label>
                <select name="period" class="form-select select2">
                    <option value="">يومي (كل اليوم)</option>
                    @foreach($periods as $p)
                    <option value="{{ $p->period_number }}" @selected(request('period') == $p->period_number)>
                        {{ $p->label }} ({{ \Carbon\Carbon::parse($p->start_time)->format('H:i') }})
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn-primary-sm w-100"><i class="bi bi-search"></i> عرض</button>
            </div>
        </form>
    </div>
</div>

@if($students->isNotEmpty())
<form action="{{ route('admin.attendance.store') }}" method="POST">
    @csrf
    <input type="hidden" name="class_id" value="{{ request('class_id') }}">
    <input type="hidden" name="date"     value="{{ request('date') }}">
    <input type="hidden" name="period"   value="{{ request('period') }}">

    <div class="panel-card">
        <div class="panel-card-header d-flex align-items-center justify-content-between">
            <h2 class="panel-card-title">
                {{ $selectedClass->name }} —
                {{ \Carbon\Carbon::parse(request('date'))->format('d/m/Y') }}
                @if(request('period')) — الحصة {{ request('period') }} @endif
            </h2>
            <div class="d-flex gap-2">
                <button type="button" class="btn-outline-sm" onclick="markAll('present')" style="font-size:.78rem">
                    ✓ حاضرون الكل
                </button>
                <button type="button" class="btn-outline-sm" onclick="markAll('absent')" style="font-size:.78rem;color:#dc2626">
                    ✕ غائبون الكل
                </button>
            </div>
        </div>
        <div class="panel-card-body p-0" style="overflow-x:auto">
            <table class="data-table" style="min-width:560px">
                <thead>
                    <tr>
                        <th>الطالب</th>
                        <th>حاضر</th>
                        <th>غائب</th>
                        <th class="d-none d-sm-table-cell">متأخر</th>
                        <th class="d-none d-sm-table-cell">بعذر</th>
                        <th class="d-none d-md-table-cell">ملاحظة</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                    @php $att = $attendance[$student->id] ?? null; @endphp
                    <tr>
                        <td style="font-weight:500">{{ $student->name }}</td>
                        <td style="text-align:center">
                            <input type="radio" name="attendance[{{ $student->id }}]" value="present" class="form-check-input att-radio att-present" @checked(($att?->status ?? 'present') === 'present')>
                        </td>
                        <td style="text-align:center">
                            <input type="radio" name="attendance[{{ $student->id }}]" value="absent" class="form-check-input att-radio att-absent" @checked(($att?->status ?? 'present') === 'absent')>
                        </td>
                        <td class="d-none d-sm-table-cell" style="text-align:center">
                            <input type="radio" name="attendance[{{ $student->id }}]" value="late" class="form-check-input att-radio att-late" @checked(($att?->status ?? 'present') === 'late')>
                        </td>
                        <td class="d-none d-sm-table-cell" style="text-align:center">
                            <input type="radio" name="attendance[{{ $student->id }}]" value="excused" class="form-check-input att-radio att-excused" @checked(($att?->status ?? 'present') === 'excused')>
                        </td>
                        <td class="d-none d-md-table-cell">
                            <input type="text" name="notes[{{ $student->id }}]"
                                   value="{{ $att?->notes }}"
                                   class="form-control form-control-sm"
                                   placeholder="ملاحظة" style="min-width:120px">
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="panel-card-body border-top">
            <button type="submit" class="btn-primary-sm">
                <i class="bi bi-save"></i> حفظ الغياب
            </button>
        </div>
    </div>
</form>
@elseif(request('class_id'))
<div class="panel-card">
    <div class="panel-card-body text-center py-5" style="color:var(--muted)">
        لا يوجد طلاب في هذا الصف.
    </div>
</div>
@endif

@push('scripts')
<script>
function markAll(status) {
    document.querySelectorAll('.att-' + status).forEach(r => r.checked = true);
}
</script>
@endpush

@endsection
