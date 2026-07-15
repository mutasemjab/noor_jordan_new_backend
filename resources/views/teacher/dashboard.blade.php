@extends('teacher.layouts.app')

@section('title', __('messages.t_dashboard'))

@section('content')

{{-- Page Header --}}
<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-title">{{ __('messages.t_dashboard') }}</h1>
        <p class="page-sub">{{ __('messages.t_welcome_back') }}, {{ $teacher->name }}!</p>
    </div>
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
            <div class="stat-icon" style="background:#eff6ff;color:#2563eb">
                <i class="bi bi-people-fill"></i>
            </div>
            <div class="stat-value">{{ number_format($teacher->total_students ?? 0) }}</div>
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
                <i class="bi bi-file-earmark-text-fill"></i>
            </div>
            <div class="stat-value">{{ $stats['total_exams'] ?? 0 }}</div>
            <div class="stat-label">{{ __('messages.exams') }}</div>
            <div class="stat-trend">
                <i class="bi bi-clipboard-check" style="color:#7c3aed"></i>
                <span style="color:var(--muted)">{{ __('messages.t_published') }}</span>
            </div>
        </div>
    </div>

</div>

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
                        <div style="color:var(--muted);font-size:.83rem">{{ $teacher->email }}</div>
                    </div>
                </div>
                @if($teacher->phone)
                <p style="font-size:.83rem;color:var(--muted)"><i class="bi bi-telephone"></i> {{ $teacher->phone }}</p>
                @endif
            </div>
        </div>
    </div>

</div>

@endsection
