@extends('admin.layouts.app')
@section('title', 'البانرات')

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-title">البانرات</h1>
        <p class="page-sub">صور السلايدر التي تظهر في التطبيق</p>
    </div>
    <a href="{{ route('admin.banners.create') }}" class="btn-primary-sm">
        <i class="bi bi-plus-lg"></i> إضافة بانر
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="panel-card">
    <div class="panel-card-body p-0">
        @if($banners->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-image" style="font-size:2.5rem;opacity:.3"></i>
                <p class="mt-2">لا توجد بانرات بعد</p>
            </div>
        @else
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width:60px">#</th>
                        <th>الصورة</th>
                        <th style="width:120px">الترتيب</th>
                        <th style="width:110px">الحالة</th>
                        <th style="width:160px">تاريخ الإضافة</th>
                        <th style="width:130px">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($banners as $banner)
                    <tr>
                        <td>{{ $banner->id }}</td>
                        <td>
                            <img src="{{ asset('assets/uploads/banners/' . $banner->image) }}"
                                 alt="banner"
                                 style="height:60px;width:160px;object-fit:cover;border-radius:8px;border:1px solid #e2e8f0">
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $banner->order_index }}</span>
                        </td>
                        <td>
                            <form action="{{ route('admin.banners.toggle', $banner->id) }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="badge border-0 {{ $banner->is_active ? 'bg-success' : 'bg-secondary' }}"
                                        style="cursor:pointer;font-size:12px;padding:5px 10px">
                                    {{ $banner->is_active ? 'نشط' : 'معطل' }}
                                </button>
                            </form>
                        </td>
                        <td>{{ $banner->created_at->format('Y-m-d') }}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.banners.edit', $banner->id) }}" class="btn-outline-sm">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST"
                                      onsubmit="return confirm('هل أنت متأكد من حذف هذا البانر؟')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-danger-sm">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

@endsection
