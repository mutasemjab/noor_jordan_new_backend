@extends('admin.layouts.app')
@section('title', 'الصفوف الدراسية')

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-title">الصفوف الدراسية</h1>
        <p class="page-sub">إدارة الصفوف ومواد كل صف ومعلميها</p>
    </div>
    <a href="{{ route('admin.classes.create') }}" class="btn-primary-sm">
        <i class="bi bi-plus-circle"></i> إضافة صف جديد
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="panel-card">
    <div class="panel-card-body p-0">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>اسم الصف</th>
                    <th>مربي الصف</th>
                    <th>عدد الطلاب</th>
                    <th>الحالة</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($classes as $class)
                <tr>
                    <td style="color:var(--muted)">{{ $class->id }}</td>
                    <td style="font-weight:600">{{ $class->name }}</td>
                    <td>
                        @if($class->homeroomTeacher)
                            <span style="font-size:.88rem">{{ $class->homeroomTeacher->name }}</span>
                        @else
                            <span style="color:var(--muted);font-size:.82rem">غير محدد</span>
                        @endif
                    </td>
                    <td>
                        <span class="pill pill-neutral">{{ $class->students_count }} طالب</span>
                    </td>
                    <td>
                        <span class="pill {{ $class->is_active ? 'pill-success' : 'pill-neutral' }}">
                            {{ $class->is_active ? 'نشط' : 'معطل' }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.classes.show', $class->id) }}" class="btn-outline-sm" style="padding:4px 10px;font-size:.78rem">
                                <i class="bi bi-journal-text"></i> إدارة المواد
                            </a>
                            <form action="{{ route('admin.classes.destroy', $class->id) }}" method="POST"
                                  onsubmit="return confirm('حذف الصف {{ $class->name }}؟')">
                                @csrf @method('DELETE')
                                <button class="btn-outline-sm" style="padding:4px 8px;color:#dc2626;border-color:#fecaca">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-4" style="color:var(--muted)">لا توجد صفوف دراسية. أضف صفاً جديداً.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
