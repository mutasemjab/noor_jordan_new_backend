@extends('teacher.layouts.app')
@section('title', __('messages.t_my_exams'))

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-title">{{ __('messages.t_my_exams') }}</h1>
        <p class="page-sub">{{ __('messages.t_exams_sub') }}</p>
    </div>
    <a href="{{ route('teacher.exams.create') }}" class="btn-primary-sm"><i class="bi bi-plus-circle"></i> {{ __('messages.t_new_exam') }}</a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="panel-card mb-3">
    <div class="panel-card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-12 col-md-4">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="{{ __('messages.t_search') }}...">
            </div>
            <div class="col-6 col-md-3">
                <select name="course_id" class="form-select form-select-sm">
                    <option value="">{{ __('messages.t_all_courses') }}</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" @selected(request('course_id') == $course->id)>{{ Str::limit($course->title_en ?: $course->title_ar, 30) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-3">
                <select name="exam_type" class="form-select form-select-sm">
                    <option value="">{{ __('messages.t_all_types') }}</option>
                    @foreach(['mock','unit','final','practice','previous_years','placement'] as $t)
                        <option value="{{ $t }}" @selected(request('exam_type') === $t)>{{ ucfirst(str_replace('_',' ',$t)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-2">
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
                    <th>{{ __('messages.t_exam') }}</th>
                    <th>{{ __('messages.t_type') }}</th>
                    <th>{{ __('messages.t_course') }}</th>
                    <th>{{ __('messages.t_questions') }}</th>
                    <th>{{ __('messages.t_duration') }}</th>
                    <th>{{ __('messages.t_status') }}</th>
                    <th>{{ __('messages.t_actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($exams as $exam)
                <tr>
                    <td>
                        <div style="font-weight:500">{{ Str::limit($exam->title_en ?: $exam->title_ar, 40) }}</div>
                        <div style="font-size:.75rem;color:var(--muted)">{{ __('messages.t_pass') }}: {{ $exam->pass_marks }}/{{ $exam->total_marks }}</div>
                    </td>
                    <td><span class="pill pill-neutral">{{ ucfirst(str_replace('_',' ',$exam->exam_type)) }}</span></td>
                    <td style="font-size:.83rem;color:var(--muted)">{{ $exam->course ? Str::limit($exam->course->title_en ?: $exam->course->title_ar, 25) : '—' }}</td>
                    <td>{{ $exam->questions->count() }}</td>
                    <td>{{ $exam->duration_minutes }} {{ __('messages.t_min') }}</td>
                    <td><span class="pill {{ $exam->is_published ? 'pill-success' : 'pill-neutral' }}">{{ $exam->is_published ? __('messages.t_live') : __('messages.t_draft') }}</span></td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('teacher.exams.show', $exam->id) }}" class="btn-primary-sm" style="padding:4px 10px" title="{{ __('messages.t_manage_questions') }}"><i class="bi bi-list-check"></i></a>
                            <a href="{{ route('teacher.exams.edit', $exam->id) }}" class="btn-outline-sm" style="padding:4px 8px"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('teacher.exams.destroy', $exam->id) }}" method="POST" onsubmit="return confirm('{{ __('messages.t_confirm_delete_exam') }}')">
                                @csrf @method('DELETE')
                                <button class="btn-outline-sm" style="padding:4px 8px;color:#dc2626;border-color:#fecaca"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-4" style="color:var(--muted)">{{ __('messages.t_no_exams_found') }}</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($exams->hasPages())
        <div class="p-3">{{ $exams->withQueryString()->links() }}</div>
        @endif
    </div>
</div>
@endsection
