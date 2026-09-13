@extends('admin.layouts.app')
@section('title', $actorType === 'teacher' ? __('messages.teacher_notes_log') : __('messages.admin_notes_log'))

@section('content')

@php
    $fieldLabels = [
        'title'       => __('messages.title'),
        'description' => __('messages.descriptions'),
        'type'        => __('messages.note_type'),
        'date'        => __('messages.date_label'),
        'class_id'    => __('messages.class_label'),
        'subject_id'  => __('messages.subject'),
        'teacher_id'  => __('messages.teacher'),
        'attachment'  => __('messages.attachment_label'),
    ];
    $actionLabels = [
        'created' => __('messages.log_action_created'),
        'updated' => __('messages.log_action_updated'),
        'deleted' => __('messages.log_action_deleted'),
    ];
    $actionPills = [
        'created' => 'pill-success',
        'updated' => 'pill-warning',
        'deleted' => 'pill-danger',
    ];
@endphp

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-title">{{ $actorType === 'teacher' ? __('messages.teacher_notes_log') : __('messages.admin_notes_log') }}</h1>
        <p class="page-sub">{{ __('messages.notes_log_sub') }}</p>
    </div>
</div>

{{-- Filters --}}
<div class="panel-card mb-3">
    <div class="panel-card-body">
        <form method="GET" class="row g-3 align-items-end">
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
            <div class="col-md-3">
                <label class="form-label">{{ __('messages.date_label') }}</label>
                <input type="date" name="date" value="{{ request('date') }}" class="form-control">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn-primary-sm"><i class="bi bi-search"></i> {{ __('messages.filter') }}</button>
                @if(request()->anyFilled(['teacher_id', 'class_id', 'date']))
                    <a href="{{ url()->current() }}" class="btn-outline-sm">
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
            <table class="table table-bordered mb-0" style="min-width:800px">
                <thead>
                    <tr>
                        <th>{{ __('messages.date_time') }}</th>
                        <th>{{ __('messages.log_actor') }}</th>
                        <th>{{ __('messages.log_action') }}</th>
                        <th>{{ __('messages.class_label') }}</th>
                        <th>{{ __('messages.teacher') }}</th>
                        <th>{{ __('messages.date_label') }}</th>
                        <th>{{ __('messages.log_changes') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td style="white-space:nowrap;font-size:.82rem">{{ $log->created_at->format('Y-m-d H:i') }}</td>
                        <td>{{ $log->actor_name }}</td>
                        <td><span class="pill {{ $actionPills[$log->action] ?? 'pill-neutral' }}">{{ $actionLabels[$log->action] ?? $log->action }}</span></td>
                        <td>{{ $log->schoolClass?->name ?? '—' }}</td>
                        <td>{{ $log->teacher?->name ?? '—' }}</td>
                        <td style="white-space:nowrap">{{ $log->note_date }}</td>
                        <td style="font-size:.82rem">
                            @foreach($log->changes as $field => $change)
                                <div>
                                    <strong>{{ $fieldLabels[$field] ?? $field }}:</strong>
                                    @if($log->action === 'created')
                                        {{ $change['new'] }}
                                    @elseif($log->action === 'deleted')
                                        {{ $change['old'] }}
                                    @else
                                        <span class="text-muted" style="text-decoration:line-through">{{ $change['old'] ?? '—' }}</span>
                                        <i class="bi bi-arrow-left mx-1"></i>
                                        <span style="font-weight:600">{{ $change['new'] ?? '—' }}</span>
                                    @endif
                                </div>
                            @endforeach
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-4" style="color:var(--muted)">{{ __('messages.no_records') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $logs->links() }}</div>
    </div>
</div>

@endsection
