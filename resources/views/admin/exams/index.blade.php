@extends('admin.layouts.app')
@section('title', __('messages.exams_title'))

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div><h1 class="page-title">{{ __('messages.exams_title') }}</h1><p class="page-sub">{{ __('messages.manage_exams_desc') }}</p></div>
    <a href="{{ route('admin.exams.create') }}" class="btn-primary-sm"><i class="bi bi-plus-circle"></i> {{ __('messages.add_exam') }}</a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="panel-card mb-3">
    <div class="panel-card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-12 col-md-4">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="{{ __('messages.search_exam_title_ph') }}">
            </div>
            <div class="col-6 col-md-2">
                <select name="exam_type" class="form-select form-select-sm">
                    <option value="">{{ __('messages.all_types') }}</option>
                    @foreach(['mock','unit','final','practice','previous_years','placement'] as $type)
                        <option value="{{ $type }}" @selected(request('exam_type') === $type)>{{ __('messages.'.$type) }}</option>
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
                <select name="course_id" class="form-select form-select-sm">
                    <option value="">{{ __('messages.all_courses') }}</option>
                    @foreach($courses as $c)
                        <option value="{{ $c->id }}" @selected(request('course_id') == $c->id)>{{ Str::limit($c->title_en ?: $c->title_ar, 25) }}</option>
                    @endforeach
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
                <tr><th>#</th><th>{{ __('messages.exam') }}</th><th>{{ __('messages.type_label') }}</th><th>{{ __('messages.questions') }}</th><th>{{ __('messages.duration') }}</th><th>{{ __('messages.attempts') }}</th><th>{{ __('messages.Status') }}</th><th>{{ __('messages.Actions') }}</th></tr>
            </thead>
            <tbody>
                @forelse($exams as $exam)
                <tr>
                    <td style="color:var(--muted)">{{ $exam->id }}</td>
                    <td>
                        <div style="font-weight:500">{{ Str::limit($exam->title_en ?: $exam->title_ar, 35) }}</div>
                        <div style="font-size:.75rem;color:var(--muted)">{{ $exam->course->title_en ?? __('messages.standalone') }}</div>
                    </td>
                    <td><span class="pill pill-info">{{ __('messages.'.$exam->exam_type) }}</span></td>
                    <td>{{ $exam->questions_count }}</td>
                    <td>{{ $exam->duration_minutes }} {{ __('messages.min_label') }}</td>
                    <td>{{ $exam->total_attempts }}</td>
                    <td><span class="pill {{ $exam->is_published ? 'pill-success' : 'pill-neutral' }}">{{ $exam->is_published ? __('messages.published') : __('messages.draft') }}</span></td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.exams.show', $exam->id) }}" class="btn-outline-sm" style="padding:4px 8px"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('admin.exams.edit', $exam->id) }}" class="btn-outline-sm" style="padding:4px 8px"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.exams.destroy', $exam->id) }}" method="POST" onsubmit="return confirm('{{ __('messages.delete_exam_confirm') }}')">
                                @csrf @method('DELETE')
                                <button class="btn-outline-sm" style="padding:4px 8px;color:#dc2626;border-color:#fecaca"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-4" style="color:var(--muted)">{{ __('messages.no_exams_found') }}</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-3">{{ $exams->withQueryString()->links() }}</div>
    </div>
</div>
@endsection
