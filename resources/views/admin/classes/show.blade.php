@extends('admin.layouts.app')
@section('title', 'إدارة الصف — ' . $class->name)

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-title">{{ $class->name }}</h1>
        <p class="page-sub">إدارة مواد الصف وتعيين المعلمين</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.classes.schedule', $class->id) }}" class="btn-outline-sm">
            <i class="bi bi-calendar3"></i> الجدول الدراسي
        </a>
        <a href="{{ route('admin.classes.videos', $class->id) }}" class="btn-outline-sm">
            <i class="bi bi-youtube"></i> الفيديوهات
        </a>
        <a href="{{ route('admin.exam-schedules.index', ['class_id' => $class->id]) }}" class="btn-outline-sm">
            <i class="bi bi-journal-check"></i> جدول الامتحانات
        </a>
        <a href="{{ route('admin.classes.index') }}" class="btn-outline-sm">
            <i class="bi bi-arrow-left"></i> رجوع
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-3">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="row g-3">

    {{-- Left: Edit class info --}}
    <div class="col-12 col-xl-4">
        <div class="panel-card">
            <div class="panel-card-header"><h2 class="panel-card-title">بيانات الصف</h2></div>
            <div class="panel-card-body">
                <form action="{{ route('admin.classes.update', $class->id) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">اسم الصف <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $class->name) }}"
                               class="form-control @error('name') is-invalid @enderror" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">مربي الصف</label>
                        <select name="homeroom_teacher_id" class="form-select select2">
                            <option value="">— بدون مربي صف —</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}"
                                    @selected(old('homeroom_teacher_id', $class->homeroom_teacher_id) == $teacher->id)>
                                    {{ $teacher->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                                   {{ $class->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">الصف نشط</label>
                        </div>
                    </div>
                    <button type="submit" class="btn-primary-sm w-100 justify-content-center">
                        <i class="bi bi-save"></i> حفظ التعديلات
                    </button>
                </form>
            </div>
        </div>

        {{-- Stats --}}
        <div class="stat-card mt-3" style="padding:16px">
            <div style="font-size:1.6rem;font-weight:800;color:var(--primary)">
                {{ $class->classSubjects->count() }}
            </div>
            <div style="font-size:.78rem;color:var(--muted)">مادة مسجلة في هذا الصف</div>
        </div>
        <div class="stat-card mt-2" style="padding:16px">
            <div style="font-size:1.6rem;font-weight:800;color:var(--primary)">
                {{ $class->students()->count() }}
            </div>
            <div style="font-size:.78rem;color:var(--muted)">طالب مسجل في هذا الصف</div>
        </div>
    </div>

    {{-- Right: Subject management --}}
    <div class="col-12 col-xl-8">

        {{-- Assigned subjects --}}
        <div class="panel-card mb-3">
            <div class="panel-card-header">
                <h2 class="panel-card-title">مواد الصف ({{ $class->classSubjects->count() }})</h2>
            </div>
            <div class="panel-card-body p-0">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>المادة</th>
                            <th>المعلم المعيّن</th>
                            <th>تغيير المعلم</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($class->classSubjects as $cs)
                        <tr>
                            <td style="font-weight:600">{{ $cs->subject->name_ar }}</td>
                            <td>
                                @if($cs->teacher)
                                    <span style="font-size:.88rem">{{ $cs->teacher->name }}</span>
                                @else
                                    <span style="color:var(--muted);font-size:.82rem">— غير معيّن —</span>
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('admin.classes.subjects.update', [$class->id, $cs->subject_id]) }}"
                                      method="POST" class="d-flex gap-1 align-items-center">
                                    @csrf @method('PUT')
                                    <select name="teacher_id" class="form-select form-select-sm select2" style="min-width:160px">
                                        <option value="">— بدون معلم —</option>
                                        @foreach($teachers as $teacher)
                                            <option value="{{ $teacher->id }}" @selected($cs->teacher_id == $teacher->id)>
                                                {{ $teacher->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn-outline-sm" style="padding:4px 8px">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </form>
                            </td>
                            <td>
                                <form action="{{ route('admin.classes.subjects.remove', [$class->id, $cs->subject_id]) }}"
                                      method="POST" onsubmit="return confirm('إزالة {{ $cs->subject->name_ar }} من الصف؟')">
                                    @csrf @method('DELETE')
                                    <button class="btn-outline-sm" style="padding:4px 8px;color:#dc2626;border-color:#fecaca">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4" style="color:var(--muted)">
                                لا توجد مواد مضافة لهذا الصف بعد.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Add subject --}}
        @if($availableSubjects->count())
        <div class="panel-card">
            <div class="panel-card-header"><h2 class="panel-card-title">إضافة مادة للصف</h2></div>
            <div class="panel-card-body">
                <form action="{{ route('admin.classes.subjects.add', $class->id) }}" method="POST">
                    @csrf
                    <div class="row g-3 align-items-end">
                        <div class="col-md-5">
                            <label class="form-label">المادة <span class="text-danger">*</span></label>
                            <select name="subject_id" class="form-select select2" required>
                                <option value="">— اختر المادة —</option>
                                @foreach($availableSubjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name_ar }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">المعلم</label>
                            <select name="teacher_id" class="form-select select2">
                                <option value="">— اختر المعلم —</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn-primary-sm w-100 justify-content-center">
                                <i class="bi bi-plus-circle"></i> إضافة
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @else
        <div class="panel-card">
            <div class="panel-card-body text-center py-4" style="color:var(--muted)">
                <i class="bi bi-check-circle" style="font-size:2rem;color:#059669"></i>
                <p class="mt-2 mb-0">تم إضافة جميع المواد النشطة لهذا الصف.</p>
            </div>
        </div>
        @endif

    </div>
</div>

@endsection
