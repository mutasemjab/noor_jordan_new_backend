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

<div class="panel-card">
    <div class="panel-card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('messages.note_type') }}</th>
                        <th>{{ __('messages.title') }}</th>
                        <th>{{ __('messages.teacher') }}</th>
                        <th>{{ __('messages.class_label') }}</th>
                        <th>{{ __('messages.date_label') }}</th>
                        <th>{{ __('messages.attachment_label') }}</th>
                        <th width="150">{{ __('messages.Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notes as $note)
                    <tr>
                        <td>{{ $note->id }}</td>
                        <td>
                            @if($note->type === 'lesson')
                                <span class="pill pill-info">{{ __('messages.note_type_lesson') }}</span>
                            @else
                                <span class="pill pill-warning">{{ __('messages.note_type_homework') }}</span>
                            @endif
                        </td>
                        <td>{{ $note->title }}</td>
                        <td>{{ $note->teacher?->name ?? '—' }}</td>
                        <td>{{ $note->schoolClass?->name ?? '—' }}</td>
                        <td>{{ $note->date?->format('Y-m-d') }}</td>
                        <td>
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
                    <tr><td colspan="8" class="text-center py-4" style="color:var(--muted)">{{ __('messages.no_records') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $notes->links() }}</div>
    </div>
</div>

@endsection
