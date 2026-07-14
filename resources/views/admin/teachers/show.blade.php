@extends('admin.layouts.app')
@section('title', $teacher->name)

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-title">{{ $teacher->name }}</h1>
        <p class="page-sub">{{ $teacher->specialization_en ?: $teacher->specialization_ar }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.teachers.edit', $teacher->id) }}" class="btn-outline-sm"><i class="bi bi-pencil"></i> {{ __('messages.Edit') }}</a>
        <a href="{{ route('admin.teachers.index') }}" class="btn-outline-sm"><i class="bi bi-arrow-left"></i> {{ __('messages.Back') }}</a>
    </div>
</div>

<div class="row g-3">

    {{-- Profile Card --}}
    <div class="col-12 col-xl-4">
        <div class="panel-card mb-3">
            <div class="panel-card-body text-center">
                @if($teacher->avatar)
                    <img src="{{ asset('assets/uploads/teachers/'.$teacher->avatar) }}" class="rounded-circle mb-3" style="width:100px;height:100px;object-fit:cover">
                @else
                    <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width:100px;height:100px;background:linear-gradient(135deg,#7c3aed,#6d28d9)">
                        <span style="color:#fff;font-size:2rem;font-weight:700">{{ strtoupper(substr($teacher->name, 0, 1)) }}</span>
                    </div>
                @endif
                <h3 style="font-size:1.1rem;font-weight:700;margin-bottom:4px">{{ $teacher->name }}</h3>
                <p style="font-size:.83rem;color:var(--muted);margin-bottom:12px">{{ $teacher->specialization_en ?: $teacher->specialization_ar }}</p>
                <div class="d-flex justify-content-center gap-2 mb-3">
                    <span class="pill {{ $teacher->is_active ? 'pill-success' : 'pill-neutral' }}">{{ $teacher->is_active ? __('messages.Active') : __('messages.Inactive') }}</span>
                    @if($teacher->is_verified)<span class="pill pill-info">{{ __('messages.verified') }}</span>@endif
                </div>
                <div style="font-size:.82rem;color:var(--muted)">
                    <div class="mb-1"><i class="bi bi-envelope"></i> {{ $teacher->email }}</div>
                    @if($teacher->phone)<div class="mb-1"><i class="bi bi-telephone"></i> {{ $teacher->phone }}</div>@endif
                    @if($teacher->years_of_experience)<div><i class="bi bi-calendar3"></i> {{ $teacher->years_of_experience }} {{ __('messages.years_experience_suffix') }}</div>@endif
                </div>
            </div>
        </div>

        <div class="panel-card mb-3">
            <div class="panel-card-header"><h2 class="panel-card-title">{{ __('messages.stats') }}</h2></div>
            <div class="panel-card-body">
                <div class="row g-2 text-center">
                    <div class="col-6">
                        <div style="font-size:1.5rem;font-weight:700;color:var(--primary)">{{ $teacher->total_courses ?? 0 }}</div>
                        <div style="font-size:.75rem;color:var(--muted)">{{ __('messages.courses') }}</div>
                    </div>
                    <div class="col-6">
                        <div style="font-size:1.5rem;font-weight:700;color:#059669">{{ $teacher->total_students ?? 0 }}</div>
                        <div style="font-size:.75rem;color:var(--muted)">{{ __('messages.students') }}</div>
                    </div>
                    <div class="col-6">
                        <div style="font-size:1.5rem;font-weight:700;color:#ea580c">{{ number_format($teacher->average_rating ?? 0, 1) }}</div>
                        <div style="font-size:.75rem;color:var(--muted)">{{ __('messages.rating') }}</div>
                    </div>
                    <div class="col-6">
                        <div style="font-size:1.5rem;font-weight:700;color:var(--text)">{{ $teacher->created_at->format('Y') }}</div>
                        <div style="font-size:.75rem;color:var(--muted)">{{ __('messages.joined') }}</div>
                    </div>
                </div>
            </div>
        </div>

        @if($teacher->bio_en || $teacher->bio_ar)
        <div class="panel-card">
            <div class="panel-card-header"><h2 class="panel-card-title">{{ __('messages.about') }}</h2></div>
            <div class="panel-card-body">
                <p style="font-size:.85rem;line-height:1.7;color:var(--text)">{{ $teacher->bio_en ?: $teacher->bio_ar }}</p>
            </div>
        </div>
        @endif
    </div>

    {{-- Courses List --}}
    <div class="col-12 col-xl-8">
        <div class="panel-card">
            <div class="panel-card-header d-flex justify-content-between align-items-center">
                <h2 class="panel-card-title">{{ __('messages.courses') }} ({{ $teacher->courses->count() }})</h2>
                <a href="{{ route('admin.courses.index') }}?teacher_id={{ $teacher->id }}" class="btn-outline-sm" style="font-size:.78rem">{{ __('messages.view_all') }}</a>
            </div>
            <div class="panel-card-body p-0">
                <table class="data-table">
                    <thead>
                        <tr><th>{{ __('messages.course') }}</th><th>{{ __('messages.students') }}</th><th>{{ __('messages.price') }}</th><th>{{ __('messages.Status') }}</th><th></th></tr>
                    </thead>
                    <tbody>
                        @forelse($teacher->courses as $course)
                        <tr>
                            <td>
                                <div style="font-weight:500;font-size:.87rem">{{ Str::limit($course->title_en ?: $course->title_ar, 40) }}</div>
                                <div style="font-size:.75rem;color:var(--muted)">{{ ucfirst($course->difficulty_level ?? 'beginner') }}</div>
                            </td>
                            <td>{{ $course->enrollments_count }}</td>
                            <td>{{ $course->is_free ? __('messages.free') : '$'.number_format($course->price, 2) }}</td>
                            <td><span class="pill {{ $course->is_published ? 'pill-success' : 'pill-neutral' }}">{{ $course->is_published ? __('messages.live') : __('messages.draft') }}</span></td>
                            <td>
                                <a href="{{ route('admin.courses.show', $course->id) }}" class="btn-outline-sm" style="padding:3px 8px;font-size:.78rem"><i class="bi bi-eye"></i></a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center py-4" style="color:var(--muted)">{{ __('messages.no_courses_yet') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
