@extends('admin.layouts.app')
@section('title', __('messages.students_title'))

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div><h1 class="page-title">{{ __('messages.students_title') }}</h1><p class="page-sub">{{ __('messages.manage_students_desc') }}</p></div>
    <div class="d-flex gap-2 flex-wrap">
        {{-- Export --}}
        <a href="{{ route('admin.students.export') }}?{{ http_build_query(request()->all()) }}" class="btn-outline-sm">
            <i class="bi bi-file-earmark-arrow-down"></i> تصدير Excel
        </a>
        {{-- Import trigger --}}
        <button class="btn-outline-sm" type="button" data-bs-toggle="collapse" data-bs-target="#import-panel">
            <i class="bi bi-file-earmark-arrow-up"></i> استيراد Excel
        </button>
        <a href="{{ route('admin.students.create') }}" class="btn-primary-sm"><i class="bi bi-plus-circle"></i> {{ __('messages.add_student') }}</a>
    </div>
</div>

{{-- Import panel --}}
<div class="collapse mb-3" id="import-panel">
    <div class="panel-card">
        <div class="panel-card-header"><h2 class="panel-card-title"><i class="bi bi-file-earmark-arrow-up"></i> استيراد الطلاب من Excel</h2></div>
        <div class="panel-card-body">
            <p class="text-muted small mb-2">الأعمدة المطلوبة: <strong>الاسم</strong> (إلزامي)، الرقم_الوطني، البريد_الإلكتروني، الهاتف، الصف، كلمة_المرور (افتراضي: Pass@1234)</p>
            <form action="{{ route('admin.students.import') }}" method="POST" enctype="multipart/form-data" class="d-flex gap-2 align-items-end flex-wrap">
                @csrf
                <div>
                    <input type="file" name="file" class="form-control form-control-sm" accept=".xlsx,.xls,.csv" required>
                </div>
                <button type="submit" class="btn-primary-sm"><i class="bi bi-upload"></i> استيراد</button>
            </form>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="panel-card mb-3">
    <div class="panel-card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-12 col-md-6">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="{{ __('messages.search_name_email_ph') }}">
            </div>
            <div class="col-6 col-md-3">
                <select name="is_active" class="form-select form-select-sm">
                    <option value="">{{ __('messages.All Status') }}</option>
                    <option value="1" @selected(request('is_active') === '1')>{{ __('messages.Active') }}</option>
                    <option value="0" @selected(request('is_active') === '0')>{{ __('messages.Inactive') }}</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <button type="submit" class="btn-primary-sm w-100"><i class="bi bi-search"></i></button>
            </div>
        </form>
    </div>
</div>

<div class="panel-card">
    <div class="panel-card-body p-0">
        <table class="data-table">
            <thead>
                <tr><th>#</th><th>{{ __('messages.student') }}</th><th>{{ __('messages.phone_label') }}</th><th>{{ __('messages.courses') }}</th><th>{{ __('messages.Status') }}</th><th>{{ __('messages.joined') }}</th><th>{{ __('messages.Actions') }}</th></tr>
            </thead>
            <tbody>
                @forelse($students as $student)
                <tr>
                    <td style="color:var(--muted)">{{ $student->id }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            @if($student->avatar)
                                <img src="{{ asset('assets/uploads/students/'.$student->avatar) }}" class="avatar avatar-sm" alt="">
                            @else
                                <div class="avatar avatar-sm" style="background:#f5f3ff;color:#7c3aed">{{ strtoupper(substr($student->name,0,1)) }}</div>
                            @endif
                            <div>
                                <div style="font-weight:500">{{ $student->name }}</div>
                                <div style="font-size:.75rem;color:var(--muted)">{{ $student->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="color:var(--muted)">{{ $student->phone ?: '—' }}</td>
                
                    <td>{{ $student->enrollments_count }}</td>
                    <td><span class="pill {{ $student->is_active ? 'pill-success' : 'pill-neutral' }}">{{ $student->is_active ? __('messages.Active') : __('messages.Inactive') }}</span></td>
                    <td style="color:var(--muted)">{{ $student->created_at->format('M d, Y') }}</td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.students.edit', $student->id) }}" class="btn-outline-sm" style="padding:4px 8px"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.students.destroy', $student->id) }}" method="POST" onsubmit="return confirm('{{ __('messages.Delete') }}?')">
                                @csrf @method('DELETE')
                                <button class="btn-outline-sm" style="padding:4px 8px;color:#dc2626;border-color:#fecaca"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-4" style="color:var(--muted)">{{ __('messages.no_students_found') }}</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-3">{{ $students->withQueryString()->links() }}</div>
    </div>
</div>
@endsection
