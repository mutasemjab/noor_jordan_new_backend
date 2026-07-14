@extends('admin.layouts.app')

@section('title', __('messages.page_dashboard'))

@section('content')

{{-- Page Header --}}
<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-title">{{ __('messages.page_dashboard') }}</h1>
        <p class="page-sub">{{ __('messages.welcome_back') }}</p>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">{{ __('messages.page_dashboard') }}</li>
        </ol>
    </nav>
</div>

{{-- Flash Message --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Stat Cards --}}
<div class="row g-3 mb-4">

    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#eff6ff;color:#2563eb">
                <i class="bi bi-mortarboard-fill"></i>
            </div>
            <div class="stat-value">{{ number_format($stats['total_students']) }}</div>
            <div class="stat-label">{{ __('messages.total_students') }}</div>
            <div class="stat-trend">
                <i class="bi bi-people trend-up"></i>
                <span style="color:var(--muted)">{{ __('messages.registered') }}</span>
            </div>
        </div>
    </div>

    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#f0fdf4;color:#059669">
                <i class="bi bi-person-workspace"></i>
            </div>
            <div class="stat-value">{{ number_format($stats['total_teachers']) }}</div>
            <div class="stat-label">{{ __('messages.active_teachers') }}</div>
            <div class="stat-trend">
                <i class="bi bi-check-circle trend-up"></i>
                <span style="color:var(--muted)">{{ __('messages.active') }}</span>
            </div>
        </div>
    </div>

    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#faf5ff;color:#7c3aed">
                <i class="bi bi-book-fill"></i>
            </div>
            <div class="stat-value">{{ number_format($stats['total_courses']) }}</div>
            <div class="stat-label">{{ __('messages.published_courses') }}</div>
            <div class="stat-trend">
                <i class="bi bi-arrow-up-right trend-up"></i>
                <span class="trend-up">{{ number_format($stats['total_enrollments']) }}</span>
                <span style="color:var(--muted)">{{ __('messages.enrollments') }}</span>
            </div>
        </div>
    </div>


</div>

{{-- Main content row --}}
<div class="row g-3 mb-3">

    {{-- {{ __('messages.recent_enrollments') }} --}}
    <div class="col-12 col-xl-8">
        <div class="panel-card h-100">
            <div class="panel-card-header">
                <h2 class="panel-card-title">{{ __('messages.recent_enrollments') }}</h2>
                <a href="{{ route('admin.students.index') }}" class="btn-outline-sm">{{ __('messages.view_all') }}</a>
            </div>
            <div class="panel-card-body p-0">
                <div style="overflow-x:auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>{{ __('messages.student') }}</th>
                                <th>{{ __('messages.course') }}</th>
                                <th>{{ __('messages.teacher') }}</th>
                                <th>{{ __('messages.date') }}</th>
                                <th>{{ __('messages.progress') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentEnrollments as $enrollment)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar avatar-sm" style="background:#eff6ff;color:#2563eb">
                                            {{ strtoupper(substr($enrollment->student->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <span style="font-weight:500">{{ $enrollment->student->name ?? '—' }}</span>
                                    </div>
                                </td>
                                <td>{{ $enrollment->course->title_en ?? $enrollment->course->title_ar ?? '—' }}</td>
                                <td style="color:var(--muted)">{{ $enrollment->course->teacher->name ?? '—' }}</td>
                                <td style="color:var(--muted)">{{ $enrollment->enrolled_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="prog-track" style="width:60px">
                                            <div class="prog-fill" style="width:{{ $enrollment->progress_percentage }}%"></div>
                                        </div>
                                        <span style="font-size:.75rem">{{ $enrollment->progress_percentage }}%</span>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4" style="color:var(--muted)">{{ __('messages.no_enrollments_yet') }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Unread Messages --}}
    <div class="col-12 col-xl-4">
        <div class="panel-card h-100">
            <div class="panel-card-header">
                <h2 class="panel-card-title">{{ __('messages.new_messages') }}</h2>
                <a href="{{ route('admin.contact_messages.index') }}" class="btn-outline-sm">{{ __('messages.view_all') }}</a>
            </div>
            <div class="panel-card-body">
                @forelse($recentContacts as $msg)
                <div class="activity-item">
                    <div class="activity-dot" style="background:#eff6ff;color:#2563eb">
                        <i class="bi bi-envelope"></i>
                    </div>
                    <div class="activity-content">
                        <div class="activity-title">{{ $msg->first_name }} {{ $msg->last_name }}</div>
                        <div class="activity-time">{{ Str::limit($msg->subject, 40) }} · {{ $msg->created_at->diffForHumans() }}</div>
                    </div>
                </div>
                @empty
                <p class="text-center py-3" style="color:var(--muted);font-size:.85rem">{{ __('messages.no_new_messages') }}</p>
                @endforelse
            </div>
        </div>
    </div>

</div>

{{-- Bottom row --}}
<div class="row g-3">

    {{-- {{ __('messages.top_courses') }} --}}
    <div class="col-12 col-md-6 col-xl-4">
        <div class="panel-card h-100">
            <div class="panel-card-header">
                <h2 class="panel-card-title">{{ __('messages.top_courses') }}</h2>
                <a href="{{ route('admin.courses.index') }}" class="btn-outline-sm">{{ __('messages.see_all') }}</a>
            </div>
            <div class="panel-card-body">
                @php $colors = ['#2563eb','#059669','#7c3aed','#ea580c','#dc2626']; @endphp
                @forelse($topCourses as $i => $course)
                @php
                    $max = $topCourses->first()->enrollments_count ?: 1;
                    $pct = (int) round(($course->enrollments_count / $max) * 100);
                @endphp
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-baseline mb-1">
                        <span style="font-size:.845rem;font-weight:500">{{ Str::limit($course->title_en ?: $course->title_ar, 28) }}</span>
                        <span style="font-size:.75rem;color:var(--muted)">{{ $course->enrollments_count }} {{ __('messages.students_count') }}</span>
                    </div>
                    <div class="prog-track">
                        <div class="prog-fill" style="width:{{ $pct }}%;background:{{ $colors[$i % 5] }}"></div>
                    </div>
                    <div style="font-size:.72rem;color:var(--muted);margin-top:3px">{{ $course->teacher->name ?? '—' }}</div>
                </div>
                @empty
                <p class="text-center py-3" style="color:var(--muted);font-size:.85rem">{{ __('messages.no_courses_yet') }}</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- {{ __('messages.platform_overview') }} --}}
    <div class="col-12 col-md-6 col-xl-4">
        <div class="panel-card h-100">
            <div class="panel-card-header">
                <h2 class="panel-card-title">{{ __('messages.platform_overview') }}</h2>
            </div>
            <div class="panel-card-body">
                @php
                    $metrics = [
                        ['bi-check-circle','#f0fdf4','#059669',__('messages.courses_completed'), number_format($stats['courses_completed'])],
                        ['bi-star-fill',   '#fff7ed','#ea580c',__('messages.avg_rating'),       number_format($stats['avg_rating'], 1) . ' / 5'],
                        ['bi-book',        '#eff6ff','#2563eb',__('messages.total_enrollments'), number_format($stats['total_enrollments'])],
                        ['bi-envelope',    '#fef2f2','#dc2626',__('messages.unread_messages'),   number_format($stats['unread_messages'])],
                    ];
                @endphp
                @foreach($metrics as $m)
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="stat-icon mb-0" style="width:42px;height:42px;border-radius:10px;background:{{ $m[1] }};color:{{ $m[2] }};font-size:1.1rem">
                        <i class="bi {{ $m[0] }}"></i>
                    </div>
                    <div>
                        <div style="font-size:.78rem;color:var(--muted)">{{ $m[3] }}</div>
                        <div style="font-size:1rem;font-weight:700">{{ $m[4] }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- {{ __('messages.quick_actions') }} --}}
    <div class="col-12 col-xl-4">
        <div class="panel-card h-100">
            <div class="panel-card-header">
                <h2 class="panel-card-title">{{ __('messages.quick_actions') }}</h2>
            </div>
            <div class="panel-card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.students.create') }}" class="btn-primary-sm justify-content-center" style="padding:12px">
                        <i class="bi bi-person-plus"></i> {{ __('messages.add_new_student') }}
                    </a>
                    <a href="{{ route('admin.teachers.create') }}" class="btn-outline-sm justify-content-center" style="padding:12px">
                        <i class="bi bi-person-workspace"></i> {{ __('messages.add_new_teacher') }}
                    </a>
                    <a href="{{ route('admin.courses.create') }}" class="btn-outline-sm justify-content-center" style="padding:12px">
                        <i class="bi bi-book"></i> {{ __('messages.create_course') }}
                    </a>
                    <a href="{{ route('admin.contact_messages.index') }}" class="btn-outline-sm justify-content-center" style="padding:12px">
                        <i class="bi bi-envelope"></i> {{ __('messages.view_messages') }}
                        @if($stats['unread_messages'] > 0)
                            <span class="pill pill-warning ms-1">{{ $stats['unread_messages'] }}</span>
                        @endif
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection
