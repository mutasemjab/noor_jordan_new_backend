@extends('admin.layouts.app')
@section('title', 'الإعلانات')

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div><h1 class="page-title">الإعلانات</h1></div>
    <a href="{{ route('admin.announcements.create') }}" class="btn-primary-sm"><i class="bi bi-plus-lg"></i> إضافة إعلان</a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="panel-card">
    <div class="panel-card-body p-0">
        <form method="GET" class="d-flex gap-2 p-3 border-bottom flex-wrap">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث في العنوان..." class="form-control" style="max-width:250px">
            <select name="class_id" class="form-select" style="max-width:200px">
                <option value="">كل الصفوف</option>
                <option value="0" @selected(request('class_id') === '0')>عام (بدون صف)</option>
                @foreach($classes as $class)
                    <option value="{{ $class->id }}" @selected(request('class_id') == $class->id)>{{ $class->name }}</option>
                @endforeach
            </select>
            <button class="btn-primary-sm" type="submit"><i class="bi bi-search"></i></button>
            <a href="{{ route('admin.announcements.index') }}" class="btn-outline-sm">إعادة تعيين</a>
        </form>

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>العنوان</th>
                        <th>الصف المستهدف</th>
                        <th>الحالة</th>
                        <th>تاريخ النشر</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($announcements as $a)
                    <tr>
                        <td>{{ $a->id }}</td>
                        <td>
                            <div class="fw-semibold">{{ $a->title }}</div>
                            <small class="text-muted">{{ Str::limit($a->body, 60) }}</small>
                        </td>
                        <td>{{ $a->schoolClass?->name ?? '<span class="badge bg-secondary">عام</span>' }}</td>
                        <td>
                            @if($a->is_active)
                                <span class="badge bg-success">نشط</span>
                            @else
                                <span class="badge bg-secondary">معطل</span>
                            @endif
                        </td>
                        <td>{{ $a->published_at?->format('Y-m-d') ?? $a->created_at->format('Y-m-d') }}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.announcements.edit', $a->id) }}" class="btn-outline-sm"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.announcements.destroy', $a->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                    @csrf @method('DELETE')
                                    <button class="btn-danger-sm"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">لا توجد إعلانات</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($announcements->hasPages())
            <div class="p-3">{{ $announcements->links() }}</div>
        @endif
    </div>
</div>

@endsection
