@extends('admin.layouts.app')
@section('title', 'العلامات')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">إدارة العلامات</h1>
        <p class="page-sub">اختر الصف والمادة لإدخال علامات الطلاب</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

{{-- Filter --}}
<div class="panel-card mb-3">
    <div class="panel-card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">الصف <span class="text-danger">*</span></label>
                <select name="class_id" class="form-select select2" required>
                    <option value="">— اختر الصف —</option>
                    @foreach($classes as $class)
                    <option value="{{ $class->id }}" @selected(request('class_id') == $class->id)>{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">المادة <span class="text-danger">*</span></label>
                <select name="subject_id" class="form-select select2" required>
                    <option value="">— اختر المادة —</option>
                    @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}" @selected(request('subject_id') == $subject->id)>{{ $subject->name_ar }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">اسم الاختبار (للفلتر)</label>
                <input type="text" name="title" value="{{ request('title') }}" class="form-control" placeholder="مثال: اختبار أول">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn-primary-sm w-100"><i class="bi bi-search"></i> عرض</button>
            </div>
        </form>
    </div>
</div>

@if($students->isNotEmpty())
<form action="{{ route('admin.grades.store') }}" method="POST">
    @csrf
    <input type="hidden" name="class_id"   value="{{ request('class_id') }}">
    <input type="hidden" name="subject_id" value="{{ request('subject_id') }}">

    {{-- Grade header --}}
    <div class="panel-card mb-3">
        <div class="panel-card-header"><h2 class="panel-card-title">بيانات الاختبار</h2></div>
        <div class="panel-card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">اسم الاختبار <span class="text-danger">*</span></label>
                    <input type="text" name="title" value="{{ old('title', request('title')) }}"
                           class="form-control" placeholder="مثال: اختبار منتصف الفصل" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">العلامة الكاملة <span class="text-danger">*</span></label>
                    <input type="number" name="max_score" value="{{ old('max_score', 100) }}"
                           class="form-control" min="1" step="0.5" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">تاريخ الاختبار <span class="text-danger">*</span></label>
                    <input type="date" name="graded_at" value="{{ old('graded_at', date('Y-m-d')) }}"
                           class="form-control" required>
                </div>
            </div>
        </div>
    </div>

    {{-- Students --}}
    <div class="panel-card">
        <div class="panel-card-header">
            <h2 class="panel-card-title">
                علامات {{ $selectedClass->name }} — {{ $selectedSubject->name_ar }}
            </h2>
        </div>
        <div class="panel-card-body p-0">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>الطالب</th>
                        <th>العلامة</th>
                        <th>النسبة %</th>
                        <th>اختبارات سابقة</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                    @php
                        $studentGrades = $grades[$student->id] ?? collect();
                        $lastGrade = $studentGrades->last();
                    @endphp
                    <tr>
                        <td style="font-weight:500">{{ $student->name }}</td>
                        <td style="width:140px">
                            <input type="number"
                                   name="grades[{{ $student->id }}]"
                                   value="{{ old("grades.{$student->id}", $lastGrade?->score) }}"
                                   class="form-control form-control-sm"
                                   min="0" step="0.5" placeholder="—">
                        </td>
                        <td id="pct-{{ $student->id }}" style="color:var(--muted);font-size:.82rem">
                            @if($lastGrade)
                                {{ $lastGrade->percentage }}%
                            @else —
                            @endif
                        </td>
                        <td style="font-size:.78rem;color:var(--muted)">
                            @foreach($studentGrades as $g)
                                <span class="pill pill-neutral" style="font-size:.68rem">{{ $g->title }}: {{ $g->score }}/{{ $g->max_score }}</span>
                            @endforeach
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="panel-card-body border-top">
            <button type="submit" class="btn-primary-sm">
                <i class="bi bi-save"></i> حفظ العلامات
            </button>
        </div>
    </div>
</form>

@elseif(request('class_id') && request('subject_id'))
<div class="panel-card">
    <div class="panel-card-body text-center py-5" style="color:var(--muted)">لا يوجد طلاب في هذا الصف.</div>
</div>
@endif

@endsection
