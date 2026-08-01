@extends('admin.layouts.app')
@section('title', 'الباصات')

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-title">الباصات</h1>
        <p class="page-sub">إدارة باصات المدرسة وسعتها</p>
    </div>
    <a href="{{ route('admin.buses.create') }}" class="btn-primary-sm">
        <i class="bi bi-plus-circle"></i> إضافة باص جديد
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="panel-card mb-3">
    <div class="panel-card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">بحث</label>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="اسم الباص">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn-primary-sm w-100"><i class="bi bi-search"></i> بحث</button>
            </div>
        </form>
    </div>
</div>

<div class="panel-card">
    <div class="panel-card-body p-0">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>اسم الباص</th>
                    <th>السعة</th>
                    <th>عدد الجولات</th>
                    <th>الحالة</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($buses as $bus)
                <tr>
                    <td style="color:var(--muted)">{{ $bus->id }}</td>
                    <td style="font-weight:600">{{ $bus->name }}</td>
                    <td>{{ $bus->capacity }} طالب</td>
                    <td><span class="pill pill-neutral">{{ $bus->trips_count }}</span></td>
                    <td>
                        <span class="pill {{ $bus->is_active ? 'pill-success' : 'pill-neutral' }}">
                            {{ $bus->is_active ? 'نشط' : 'معطل' }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.buses.edit', $bus->id) }}" class="btn-outline-sm" style="padding:4px 10px;font-size:.78rem">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.buses.destroy', $bus->id) }}" method="POST"
                                  onsubmit="return confirm('حذف الباص {{ $bus->name }}؟')">
                                @csrf @method('DELETE')
                                <button class="btn-outline-sm" style="padding:4px 8px;color:#dc2626;border-color:#fecaca">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-4" style="color:var(--muted)">لا توجد باصات مضافة بعد.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3">{{ $buses->links() }}</div>
</div>

@endsection
