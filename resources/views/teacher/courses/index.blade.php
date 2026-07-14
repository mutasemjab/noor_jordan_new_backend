@extends('teacher.layouts.app')
@section('title', __('messages.t_my_courses'))

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-title">{{ __('messages.t_my_courses') }}</h1>
        <p class="page-sub">{{ __('messages.t_courses_sub') }}</p>
    </div>
    <a href="{{ route('teacher.courses.create') }}" class="btn-primary-sm"><i class="bi bi-plus-circle"></i> {{ __('messages.t_new_course') }}</a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="panel-card mb-3">
    <div class="panel-card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-12 col-md-5">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="{{ __('messages.t_search') }}...">
            </div>
            <div class="col-6 col-md-3">
                <select name="is_published" class="form-select form-select-sm">
                    <option value="">{{ __('messages.t_all') }}</option>
                    <option value="1" @selected(request('is_published') === '1')>{{ __('messages.t_published') }}</option>
                    <option value="0" @selected(request('is_published') === '0')>{{ __('messages.t_draft') }}</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <button type="submit" class="btn-primary-sm w-100"><i class="bi bi-search"></i></button>
            </div>
        </form>
    </div>
</div>

<div class="row g-3">
    @forelse($courses as $course)
    <div class="col-12 col-md-6 col-xl-4">
        <div class="panel-card h-100">
            @if($course->thumbnail)
                <img src="{{ asset('uploads/courses/'.$course->thumbnail) }}" class="w-100" style="height:150px;object-fit:cover;border-radius:12px 12px 0 0" alt="">
            @else
                <div style="height:80px;background:linear-gradient(135deg,#7c3aed,#6d28d9);border-radius:12px 12px 0 0;display:flex;align-items:center;justify-content:center">
                    <i class="bi bi-book" style="color:#fff;font-size:2rem"></i>
                </div>
            @endif
            <div class="panel-card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h3 style="font-size:.95rem;font-weight:600;flex:1">{{ Str::limit($course->title_en ?: $course->title_ar, 45) }}</h3>
                    <span class="pill {{ $course->is_published ? 'pill-success' : 'pill-neutral' }} ms-2">
                        {{ $course->is_published ? __('messages.t_live') : __('messages.t_draft') }}
                    </span>
                </div>
                <div class="d-flex gap-3 mb-3" style="font-size:.8rem;color:var(--muted)">
                    <span><i class="bi bi-people"></i> {{ $course->enrollments_count }} {{ __('messages.t_students_enrolled') }}</span>
                    <span><i class="bi bi-star-fill" style="color:#ea580c"></i> {{ number_format($course->average_rating, 1) }}</span>
                    <span><i class="bi bi-currency-dollar"></i> {{ $course->is_free ? __('messages.t_free') : number_format($course->price) }}</span>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('teacher.courses.show', $course->id) }}" class="btn-primary-sm flex-1 justify-content-center">
                        <i class="bi bi-layout-text-sidebar"></i> {{ __('messages.t_manage') }}
                    </a>
                    <a href="{{ route('teacher.courses.edit', $course->id) }}" class="btn-outline-sm" style="padding:6px 10px"><i class="bi bi-pencil"></i></a>
                    <form action="{{ route('teacher.courses.destroy', $course->id) }}" method="POST" onsubmit="return confirm('{{ __('messages.t_confirm_delete') }}')">
                        @csrf @method('DELETE')
                        <button class="btn-outline-sm" style="padding:6px 10px;color:#dc2626;border-color:#fecaca"><i class="bi bi-trash"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="panel-card text-center py-5" style="color:var(--muted)">
            <i class="bi bi-book" style="font-size:3rem;display:block;margin-bottom:12px"></i>
            <p>{{ __('messages.t_no_courses_yet') }}</p>
            <a href="{{ route('teacher.courses.create') }}" class="btn-primary-sm">{{ __('messages.t_create_first_course') }}</a>
        </div>
    </div>
    @endforelse
</div>

@if($courses->hasPages())
<div class="mt-3">{{ $courses->withQueryString()->links() }}</div>
@endif
@endsection
