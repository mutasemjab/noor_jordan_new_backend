@extends('admin.layouts.app')
@section('title', __('messages.courses_title'))

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-title">{{ __('messages.courses_title') }}</h1>
        <p class="page-sub">{{ __('messages.manage_courses_desc') }}</p>
    </div>
    <a href="{{ route('admin.courses.create') }}" class="btn-primary-sm">
        <i class="bi bi-plus-circle"></i> {{ __('messages.add_course') }}
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
        {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Filters --}}
<div class="panel-card mb-3">
    <div class="panel-card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-12 col-md-4">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="{{ __('messages.search_title_ph') }}">
            </div>
            <div class="col-6 col-md-3">
                <select name="category_id" class="form-select form-select-sm">
                    <option value="">{{ __('messages.all_categories') }}</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(request('category_id') == $cat->id)>{{ $cat->name_en }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <select name="is_published" class="form-select form-select-sm">
                    <option value="">{{ __('messages.All Status') }}</option>
                    <option value="1" @selected(request('is_published') === '1')>{{ __('messages.published') }}</option>
                    <option value="0" @selected(request('is_published') === '0')>{{ __('messages.draft') }}</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <select name="teacher_id" class="form-select form-select-sm">
                    <option value="">{{ __('messages.all_teachers') }}</option>
                    @foreach($teachers as $t)
                        <option value="{{ $t->id }}" @selected(request('teacher_id') == $t->id)>{{ $t->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-1">
                <button type="submit" class="btn-primary-sm w-100"><i class="bi bi-search"></i></button>
            </div>
        </form>
    </div>
</div>

{{-- Table --}}
<div class="panel-card">
    <div class="panel-card-body p-0">
        <div style="overflow-x:auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('messages.course') }}</th>
                        <th>{{ __('messages.teacher') }}</th>
                        <th>{{ __('messages.category') }}</th>
                        <th>{{ __('messages.price') }}</th>
                        <th>{{ __('messages.students') }}</th>
                        <th>{{ __('messages.Status') }}</th>
                        <th>{{ __('messages.Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($courses as $course)
                    <tr>
                        <td style="color:var(--muted)">{{ $course->id }}</td>
                        <td>
                            <div style="font-weight:500">{{ Str::limit($course->title_en ?: $course->title_ar, 35) }}</div>
                            <div style="font-size:.72rem;color:var(--muted)">{{ $course->title_ar }}</div>
                        </td>
                        <td style="color:var(--muted)">{{ $course->teacher->name ?? '—' }}</td>
                        <td><span class="pill pill-info">{{ $course->category->name_en ?? '—' }}</span></td>
                        <td>
                            @if($course->is_free)
                                <span class="pill pill-success">{{ __('messages.free') }}</span>
                            @else
                                <span style="font-weight:600">${{ number_format($course->price, 0) }}</span>
                                @if($course->old_price)
                                    <span style="text-decoration:line-through;color:var(--muted);font-size:.78rem">${{ number_format($course->old_price, 0) }}</span>
                                @endif
                            @endif
                        </td>
                        <td>{{ $course->enrollments_count }}</td>
                        <td>
                            <span class="pill {{ $course->is_published ? 'pill-success' : 'pill-neutral' }}">
                                {{ $course->is_published ? __('messages.published') : __('messages.draft') }}
                            </span>
                            @if($course->is_featured)
                                <span class="pill pill-warning">{{ __('messages.featured') }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.courses.show', $course->id) }}" class="btn-outline-sm" style="padding:4px 8px" title="{{ __('messages.View') }}">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.courses.edit', $course->id) }}" class="btn-outline-sm" style="padding:4px 8px" title="{{ __('messages.Edit') }}">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.courses.destroy', $course->id) }}" method="POST"
                                      onsubmit="return confirm('{{ __('messages.delete_course_confirm') }}')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-outline-sm" style="padding:4px 8px;color:#dc2626;border-color:#fecaca" title="{{ __('messages.Delete') }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4" style="color:var(--muted)">{{ __('messages.no_courses_found') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $courses->withQueryString()->links() }}</div>
    </div>
</div>

@endsection
