@extends('teacher.layouts.app')

@section('title', __('messages.t_dashboard'))

@section('content')

{{-- Page Header --}}
<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-title">{{ __('messages.t_dashboard') }}</h1>
        <p class="page-sub">{{ __('messages.t_welcome_back') }}, {{ $teacher->name }}!</p>
    </div>
    <a href="{{ route('teacher.courses.create') }}" class="btn-primary-sm">
        <i class="bi bi-plus-circle"></i> {{ __('messages.t_create_course') }}
    </a>
</div>

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
            <div class="stat-icon" style="background:#ecfdf5;color:#059669">
                <i class="bi bi-book-fill"></i>
            </div>
            <div class="stat-value">{{ $stats['total_courses'] }}</div>
            <div class="stat-label">{{ __('messages.t_my_courses') }}</div>
            <div class="stat-trend">
                <i class="bi bi-book trend-up"></i>
                <span style="color:var(--muted)">{{ __('messages.t_published') }}</span>
            </div>
        </div>
    </div>

    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#eff6ff;color:#2563eb">
                <i class="bi bi-people-fill"></i>
            </div>
            <div class="stat-value">{{ number_format($stats['total_students']) }}</div>
            <div class="stat-label">{{ __('messages.t_total_students') }}</div>
            <div class="stat-trend">
                <i class="bi bi-arrow-up-right trend-up"></i>
                <span style="color:var(--muted)">{{ __('messages.t_enrolled') }}</span>
            </div>
        </div>
    </div>



    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#faf5ff;color:#7c3aed">
                <i class="bi bi-star-fill"></i>
            </div>
            <div class="stat-value">{{ number_format($stats['avg_rating'], 1) }}</div>
            <div class="stat-label">{{ __('messages.t_avg_rating') }}</div>
            <div class="stat-trend">
                <i class="bi bi-star" style="color:#ea580c"></i>
                <span style="color:var(--muted)">{{ __('messages.t_out_of_5') }}</span>
            </div>
        </div>
    </div>

</div>

<div class="row g-3 mb-3">

    {{-- My Courses --}}
    <div class="col-12 col-xl-7">
        <div class="panel-card h-100">
            <div class="panel-card-header">
                <h2 class="panel-card-title">{{ __('messages.t_my_courses') }}</h2>
                <a href="{{ route('teacher.courses.index') }}" class="btn-outline-sm">{{ __('messages.t_all_courses') }}</a>
            </div>
            <div class="panel-card-body p-0">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>{{ __('messages.t_course') }}</th>
                            <th>{{ __('messages.t_students') }}</th>
                            <th>{{ __('messages.t_rating') }}</th>
                            <th>{{ __('messages.t_status') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($myCourses as $course)
                        <tr>
                            <td style="font-weight:500">{{ Str::limit($course->title_en ?: $course->title_ar, 32) }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-1">
                                    <i class="bi bi-people" style="color:var(--muted);font-size:.8rem"></i>
                                    <span>{{ $course->enrollments_count }}</span>
                                </div>
                            </td>
                            <td>
                                <span style="color:#ea580c;font-weight:600">
                                    <i class="bi bi-star-fill" style="font-size:.75rem"></i> {{ number_format($course->average_rating, 1) }}
                                </span>
                            </td>
                            <td>
                                <span class="pill {{ $course->is_published ? 'pill-success' : 'pill-neutral' }}">
                                    {{ $course->is_published ? __('messages.t_published') : __('messages.t_draft') }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('teacher.courses.show', $course->id) }}" class="btn-outline-sm" style="padding:4px 10px;font-size:.75rem">
                                    {{ __('messages.t_manage') }}
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4" style="color:var(--muted)">
                                {{ __('messages.t_no_courses_yet') }}. <a href="{{ route('teacher.courses.create') }}">{{ __('messages.t_create_first_course') }}</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Recent Enrollments --}}
    <div class="col-12 col-xl-5">
        <div class="panel-card h-100">
            <div class="panel-card-header">
                <h2 class="panel-card-title">{{ __('messages.t_recent_enrollments') }}</h2>
            </div>
            <div class="panel-card-body">
                @forelse($recentEnrollments as $enrollment)
                <div class="d-flex align-items-center gap-3 mb-3 pb-3" style="border-bottom:1px solid var(--border)">
                    <div class="avatar avatar-sm" style="background:var(--primary-light);color:var(--primary)">
                        {{ strtoupper(substr($enrollment->student->name ?? 'U', 0, 1)) }}
                    </div>
                    <div style="flex:1">
                        <div style="font-size:.845rem;font-weight:500">{{ $enrollment->student->name ?? '—' }}</div>
                        <div style="font-size:.75rem;color:var(--muted)">{{ Str::limit($enrollment->course->title_en ?: $enrollment->course->title_ar, 30) }}</div>
                    </div>
                    <div class="text-end">
                        <div style="font-size:.72rem;color:var(--muted)">{{ $enrollment->enrolled_at->diffForHumans() }}</div>
                        <div style="font-size:.78rem;font-weight:600;color:var(--primary)">{{ $enrollment->progress_percentage }}%</div>
                    </div>
                </div>
                @empty
                <p class="text-center py-3" style="color:var(--muted);font-size:.85rem">{{ __('messages.t_no_enrollments_yet') }}</p>
                @endforelse
            </div>
        </div>
    </div>

</div>

{{-- Bottom row --}}
<div class="row g-3">

    {{-- Profile Info --}}
    <div class="col-12 col-md-6">
        <div class="panel-card h-100">
            <div class="panel-card-header">
                <h2 class="panel-card-title">{{ __('messages.t_my_profile') }}</h2>
                <a href="{{ route('teacher.profile') }}" class="btn-outline-sm">{{ __('messages.t_edit_profile') }}</a>
            </div>
            <div class="panel-card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    @if($teacher->avatar)
                        <img src="{{ asset('uploads/teachers/' . $teacher->avatar) }}" class="avatar" style="width:56px;height:56px" alt="">
                    @else
                        <div class="avatar" style="width:56px;height:56px;background:var(--primary-light);color:var(--primary);font-size:1.4rem">
                            {{ strtoupper(substr($teacher->name, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <div style="font-weight:600;font-size:1rem">{{ $teacher->name }}</div>
                        <div style="color:var(--muted);font-size:.83rem">{{ $teacher->specialization_en ?: $teacher->specialization_ar ?: __('messages.teacher') }}</div>
                    </div>
                </div>
                @if($teacher->bio_en ?: $teacher->bio_ar)
                <p style="font-size:.83rem;color:var(--muted)">{{ Str::limit($teacher->bio_en ?: $teacher->bio_ar, 120) }}</p>
                @endif
                <div class="d-flex gap-2 flex-wrap">

                    @if($teacher->is_verified)
                    <span class="pill pill-success"><i class="bi bi-patch-check"></i> {{ __('messages.t_verified') }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>



</div>

@endsection
