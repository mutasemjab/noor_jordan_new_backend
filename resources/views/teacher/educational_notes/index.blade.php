@extends('teacher.layouts.app')
@section('title', __('messages.educational_notes'))

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-title">{{ __('messages.educational_notes') }}</h1>
        <p class="page-sub">{{ __('messages.educational_notes_sub') }}</p>
    </div>
    <a href="{{ route('teacher.educational-notes.create') }}" class="btn-primary-sm">
        <i class="bi bi-plus-circle"></i> {{ __('messages.add_new') }}
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="row g-3">
    @forelse($notes as $note)
    <div class="col-12 col-md-6 col-xl-4">
        <div class="panel-card h-100">
            <div class="panel-card-body">
                <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                    @if($note->type === 'lesson')
                        <span class="pill pill-info"><i class="bi bi-book"></i> {{ __('messages.note_type_lesson') }}</span>
                    @else
                        <span class="pill pill-warning"><i class="bi bi-pencil-square"></i> {{ __('messages.note_type_homework') }}</span>
                    @endif
                    <span style="font-size:.78rem;color:var(--muted)">{{ $note->date?->format('Y-m-d') }}</span>
                </div>
                <h3 style="font-size:1rem;font-weight:600;margin-bottom:6px">{{ $note->title }}</h3>
                @if($note->schoolClass)
                    <div style="font-size:.8rem;color:var(--muted);margin-bottom:6px">
                        <i class="bi bi-people"></i> {{ $note->schoolClass->name }}
                    </div>
                @endif
                @if($note->description)
                    <p style="font-size:.85rem;color:var(--muted);margin-bottom:10px">{{ $note->description }}</p>
                @endif
                @if($note->attachment)
                    <a href="{{ asset('assets/uploads/educational_notes/'.$note->attachment) }}" target="_blank"
                       style="font-size:.8rem;color:var(--primary)">
                        <i class="bi bi-paperclip"></i> {{ __('messages.view_attachment') }}
                    </a>
                @endif
            </div>
            <div class="panel-card-footer d-flex gap-2" style="padding:10px 16px;border-top:1px solid var(--border)">
                <a href="{{ route('teacher.educational-notes.edit', $note->id) }}" class="btn-outline-sm" style="font-size:.8rem;padding:4px 10px">
                    <i class="bi bi-pencil"></i> {{ __('messages.Edit') }}
                </a>
                <form method="POST" action="{{ route('teacher.educational-notes.destroy', $note->id) }}">
                    @csrf @method('DELETE')
                    <button class="btn-outline-sm" style="font-size:.8rem;padding:4px 10px;color:#dc2626;border-color:#fecaca"
                            onclick="return confirm('{{ __('messages.delete_confirm') }}')">
                        <i class="bi bi-trash"></i> {{ __('messages.Delete') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="panel-card">
            <div class="panel-card-body text-center py-5">
                <div style="font-size:48px;margin-bottom:12px">📓</div>
                <p style="color:var(--muted)">{{ __('messages.no_notes_yet') }}</p>
                <a href="{{ route('teacher.educational-notes.create') }}" class="btn-primary-sm">
                    <i class="bi bi-plus-circle"></i> {{ __('messages.add_note') }}
                </a>
            </div>
        </div>
    </div>
    @endforelse
</div>

@if($notes->hasPages())
    <div class="mt-3">{{ $notes->links() }}</div>
@endif

@endsection
