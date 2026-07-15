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
            <div class="stat-icon" style="background:#fef2f2;color:#dc2626">
                <i class="bi bi-envelope-fill"></i>
            </div>
            <div class="stat-value">{{ number_format($stats['unread_messages']) }}</div>
            <div class="stat-label">{{ __('messages.unread_messages') }}</div>
            <div class="stat-trend">
                <i class="bi bi-envelope" style="color:#dc2626"></i>
                <span style="color:var(--muted)">{{ __('messages.new_messages') }}</span>
            </div>
        </div>
    </div>

</div>

{{-- Main content row --}}
<div class="row g-3 mb-3">

    {{-- Unread Messages --}}
    <div class="col-12 col-xl-8">
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

    {{-- Quick Actions --}}
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
