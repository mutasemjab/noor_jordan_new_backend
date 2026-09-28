@extends('admin.layouts.app')
@section('title', __('messages.external_links'))

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-title">{{ __('messages.external_links') }}</h1>
        <p class="page-sub">{{ __('messages.external_links_sub') }}</p>
    </div>
    <a href="{{ route('admin.external-links.create') }}" class="btn-primary-sm">
        <i class="bi bi-plus-circle"></i> {{ __('messages.add_new') }}
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

{{-- Filters --}}
<div class="panel-card mb-3">
    <div class="panel-card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">{{ __('messages.class_label') }}</label>
                <select name="class_id" class="form-select select2">
                    <option value="">— {{ __('messages.select_class') }} —</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" @selected(request('class_id') == $class->id)>{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">{{ __('messages.teacher') }}</label>
                <select name="teacher_id" class="form-select select2">
                    <option value="">— {{ __('messages.select_teacher') }} —</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}" @selected(request('teacher_id') == $teacher->id)>{{ $teacher->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">{{ __('messages.subject') }}</label>
                <select name="subject_id" class="form-select select2">
                    <option value="">— {{ __('messages.select_subject') }} —</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" @selected(request('subject_id') == $subject->id)>{{ $subject->name_ar }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn-primary-sm"><i class="bi bi-search"></i> {{ __('messages.filter') }}</button>
                @if(request()->anyFilled(['class_id', 'teacher_id', 'subject_id']))
                    <a href="{{ route('admin.external-links.index') }}" class="btn-outline-sm">
                        <i class="bi bi-x-circle"></i> {{ __('messages.clear_filters') }}
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="panel-card">
    <div class="panel-card-body p-0">
        <div style="overflow-x:auto">
            <table class="table table-bordered mb-0" style="min-width:600px">
                <thead>
                    <tr>
                        <th>{{ __('messages.class_label') }}</th>
                        <th>{{ __('messages.teacher') }}</th>
                        <th>{{ __('messages.subject') }}</th>
                        <th>{{ __('messages.link_url') }}</th>
                        <th>{{ __('messages.Status') }}</th>
                        <th width="150">{{ __('messages.Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($links as $link)
                    <tr>
                        <td>{{ $link->schoolClass?->name ?? '—' }}</td>
                        <td>{{ $link->teacher?->name ?? '—' }}</td>
                        <td>{{ $link->subject?->name_ar ?? '—' }}</td>
                        <td>
                            <a href="{{ $link->url }}" target="_blank" style="word-break:break-all">
                                <i class="bi bi-box-arrow-up-right"></i> {{ \Illuminate\Support\Str::limit($link->url, 40) }}
                            </a>
                        </td>
                        <td>
                            {!! $link->status
                                ? '<span class="pill pill-success">'.__('messages.Active').'</span>'
                                : '<span class="pill pill-neutral">'.__('messages.Inactive').'</span>' !!}
                        </td>
                        <td>
                            <a href="{{ route('admin.external-links.edit', $link->id) }}" class="btn btn-warning btn-sm">
                                {{ __('messages.Edit') }}
                            </a>
                            <form method="POST" action="{{ route('admin.external-links.destroy', $link->id) }}" style="display:inline-block">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm" onclick="return confirm('{{ __('messages.delete_confirm') }}')">
                                    {{ __('messages.Delete') }}
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4" style="color:var(--muted)">{{ __('messages.no_records') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $links->links() }}</div>
    </div>
</div>

@endsection
