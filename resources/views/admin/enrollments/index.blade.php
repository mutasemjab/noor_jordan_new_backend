@extends('admin.layouts.app')
@section('title', __('messages.enrollments_title'))

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-title">{{ __('messages.enrollments_title') }}</h1>
        <p class="page-sub">{{ __('messages.enrollments_desc') }}</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="panel-card mb-3">
    <div class="panel-card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-12 col-md-4">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="{{ __('messages.search_student_ph') }}">
            </div>
            <div class="col-12 col-md-3">
                <select name="course_id" class="form-select form-select-sm">
                    <option value="">{{ __('messages.all_courses') }}</option>
                    @foreach($courses as $course)
                    <option value="{{ $course->id }}" @selected(request('course_id') == $course->id)>
                        {{ $course->title_en ?: $course->title_ar }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
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
                <tr>
                    <th>#</th>
                    <th>{{ __('messages.student') }}</th>
                    <th>{{ __('messages.course') }}</th>
                    <th>{{ __('messages.progress') }}</th>
                    <th>{{ __('messages.enrolled_at') }}</th>
                    <th>{{ __('messages.Status') }}</th>
                    <th>{{ __('messages.Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($enrollments as $en)
                <tr>
                    <td style="color:var(--muted)">{{ $en->id }}</td>
                    <td>
                        <div style="font-weight:500">{{ $en->student->name ?? '—' }}</div>
                        <div style="font-size:.75rem;color:var(--muted)">{{ $en->student->email ?? '' }}</div>
                    </td>
                    <td>
                        <div style="font-size:.85rem;font-weight:500">{{ $en->course->title_en ?? $en->course->title_ar ?? '—' }}</div>
                        @if($en->course && !$en->course->is_free)
                            <span class="pill pill-info" style="font-size:.65rem">{{ __('messages.paid') }}</span>
                        @else
                            <span class="pill pill-success" style="font-size:.65rem">{{ __('messages.free') }}</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div style="flex:1;height:6px;background:#e2e8f0;border-radius:4px;overflow:hidden">
                                <div style="height:6px;background:var(--primary);border-radius:4px;width:{{ $en->progress_percentage ?? 0 }}%"></div>
                            </div>
                            <span style="font-size:.75rem;color:var(--muted)">{{ $en->progress_percentage ?? 0 }}%</span>
                        </div>
                        @if($en->is_completed)
                            <span class="pill pill-success" style="font-size:.65rem">{{ __('messages.completed') }}</span>
                        @endif
                    </td>
                    <td style="color:var(--muted);font-size:.83rem">{{ $en->created_at->format('Y-m-d') }}</td>
                    <td>
                        <span class="pill {{ $en->is_active ? 'pill-success' : 'pill-neutral' }}">
                            {{ $en->is_active ? __('messages.Active') : __('messages.Inactive') }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <form action="{{ route('admin.enrollments.toggle', $en->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <button class="btn-outline-sm" style="padding:4px 8px" title="{{ $en->is_active ? __('messages.deactivate') : __('messages.activate') }}">
                                    <i class="bi bi-{{ $en->is_active ? 'pause-circle' : 'play-circle' }}"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.enrollments.destroy', $en->id) }}" method="POST"
                                  onsubmit="return confirm('{{ __('messages.delete_enrollment_confirm') }}')">
                                @csrf @method('DELETE')
                                <button class="btn-outline-sm" style="padding:4px 8px;color:#dc2626;border-color:#fecaca"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-4" style="color:var(--muted)">{{ __('messages.no_enrollments_yet') }}</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-3">{{ $enrollments->withQueryString()->links() }}</div>
    </div>
</div>

@endsection
