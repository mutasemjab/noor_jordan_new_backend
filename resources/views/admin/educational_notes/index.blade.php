@extends('admin.layouts.app')
@section('title', __('messages.educational_notes'))

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-title">{{ __('messages.educational_notes') }}</h1>
        <p class="page-sub">{{ __('messages.educational_notes_sub') }}</p>
    </div>
    <a href="{{ route('admin.educational-notes.create') }}" class="btn-primary-sm">
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
            <div class="col-md-2">
                <label class="form-label">{{ __('messages.note_type') }}</label>
                <select name="type" class="form-select select2">
                    <option value="">{{ __('messages.All Status') }}</option>
                    <option value="lesson" @selected(request('type') === 'lesson')>{{ __('messages.note_type_lesson') }}</option>
                    <option value="homework" @selected(request('type') === 'homework')>{{ __('messages.note_type_homework') }}</option>
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
                <label class="form-label">{{ __('messages.class_label') }}</label>
                <select name="class_id" class="form-select select2">
                    <option value="">— {{ __('messages.select_class') }} —</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" @selected(request('class_id') == $class->id)>{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">{{ __('messages.date_from') }}</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
            </div>
            <div class="col-md-2">
                <label class="form-label">{{ __('messages.date_to') }}</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
            </div>
            <div class="col-md-12 d-flex gap-2">
                <button type="submit" class="btn-primary-sm"><i class="bi bi-search"></i> {{ __('messages.filter') }}</button>
                @if(request()->anyFilled(['type', 'teacher_id', 'class_id', 'date_from', 'date_to']))
                    <a href="{{ route('admin.educational-notes.index') }}" class="btn-outline-sm">
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
                        <th class="d-none d-md-table-cell">#</th>
                        <th class="d-none d-sm-table-cell">{{ __('messages.note_type') }}</th>
                        <th>{{ __('messages.title') }}</th>
                        <th class="d-none d-md-table-cell">{{ __('messages.teacher') }}</th>
                        <th class="d-none d-sm-table-cell">{{ __('messages.class_label') }}</th>
                        <th class="d-none d-lg-table-cell">{{ __('messages.date_label') }}</th>
                        <th class="d-none d-lg-table-cell">{{ __('messages.attachment_label') }}</th>
                        <th width="150">{{ __('messages.Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notes as $note)
                    <tr>
                        <td class="d-none d-md-table-cell">{{ $note->id }}</td>
                        <td class="d-none d-sm-table-cell">
                            @if($note->type === 'lesson')
                                <span class="pill pill-info">{{ __('messages.note_type_lesson') }}</span>
                            @else
                                <span class="pill pill-warning">{{ __('messages.note_type_homework') }}</span>
                            @endif
                        </td>
                        <td>
                            {{ $note->title }}
                            <div class="d-sm-none" style="font-size:.72rem;color:var(--muted)">{{ $note->schoolClass?->name ?? '' }}</div>
                        </td>
                        <td class="d-none d-md-table-cell">{{ $note->teacher?->name ?? '—' }}</td>
                        <td class="d-none d-sm-table-cell">{{ $note->schoolClass?->name ?? '—' }}</td>
                        <td class="d-none d-lg-table-cell">{{ $note->date?->format('Y-m-d') }}</td>
                        <td class="d-none d-lg-table-cell">
                            @if($note->attachment)
                                <a href="{{ asset('assets/uploads/educational_notes/'.$note->attachment) }}" target="_blank">
                                    <i class="bi bi-paperclip"></i> {{ __('messages.view_attachment') }}
                                </a>
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.educational-notes.edit', $note->id) }}" class="btn btn-warning btn-sm">
                                {{ __('messages.Edit') }}
                            </a>
                            <form method="POST" action="{{ route('admin.educational-notes.destroy', $note->id) }}" style="display:inline-block">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm" onclick="return confirm('{{ __('messages.delete_confirm') }}')">
                                    {{ __('messages.Delete') }}
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="2" class="text-center py-4" style="color:var(--muted)">{{ __('messages.no_records') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $notes->links() }}</div>
    </div>
</div>

@endsection
