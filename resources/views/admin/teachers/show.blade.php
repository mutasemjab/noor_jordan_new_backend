@extends('admin.layouts.app')
@section('title', $teacher->name)

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-title">{{ $teacher->name }}</h1>
        <p class="page-sub">{{ $teacher->email }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.teachers.edit', $teacher->id) }}" class="btn-outline-sm"><i class="bi bi-pencil"></i> {{ __('messages.Edit') }}</a>
        <a href="{{ route('admin.teachers.index') }}" class="btn-outline-sm"><i class="bi bi-arrow-left"></i> {{ __('messages.Back') }}</a>
    </div>
</div>

<div class="row g-3">

    {{-- Profile Card --}}
    <div class="col-12 col-xl-4">
        <div class="panel-card mb-3">
            <div class="panel-card-body text-center">
                @if($teacher->avatar)
                    <img src="{{ asset('assets/uploads/teachers/'.$teacher->avatar) }}" class="rounded-circle mb-3" style="width:100px;height:100px;object-fit:cover">
                @else
                    <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width:100px;height:100px;background:linear-gradient(135deg,#7c3aed,#6d28d9)">
                        <span style="color:#fff;font-size:2rem;font-weight:700">{{ strtoupper(substr($teacher->name, 0, 1)) }}</span>
                    </div>
                @endif
                <h3 style="font-size:1.1rem;font-weight:700;margin-bottom:4px">{{ $teacher->name }}</h3>
                <div class="d-flex justify-content-center gap-2 mb-3">
                    <span class="pill {{ $teacher->is_active ? 'pill-success' : 'pill-neutral' }}">{{ $teacher->is_active ? __('messages.Active') : __('messages.Inactive') }}</span>
                </div>
                <div style="font-size:.82rem;color:var(--muted)">
                    <div class="mb-1"><i class="bi bi-envelope"></i> {{ $teacher->email }}</div>
                    @if($teacher->phone)<div class="mb-1"><i class="bi bi-telephone"></i> {{ $teacher->phone }}</div>@endif
                </div>
            </div>
        </div>

        <div class="panel-card mb-3">
            <div class="panel-card-header"><h2 class="panel-card-title">{{ __('messages.stats') }}</h2></div>
            <div class="panel-card-body">
                <div class="row g-2 text-center">
                    <div class="col-6">
                        <div style="font-size:1.5rem;font-weight:700;color:#059669">{{ $teacher->total_students ?? 0 }}</div>
                        <div style="font-size:.75rem;color:var(--muted)">{{ __('messages.students') }}</div>
                    </div>
                    <div class="col-6">
                        <div style="font-size:1.5rem;font-weight:700;color:var(--text)">{{ $teacher->created_at->format('Y') }}</div>
                        <div style="font-size:.75rem;color:var(--muted)">{{ __('messages.joined') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Subjects List --}}
    <div class="col-12 col-xl-8">
        <div class="panel-card">
            <div class="panel-card-header d-flex justify-content-between align-items-center">
                <h2 class="panel-card-title">{{ __('messages.subjects') }} ({{ $teacher->subjects->count() }})</h2>
            </div>
            <div class="panel-card-body p-0">
                <table class="data-table">
                    <thead>
                        <tr><th>#</th><th>{{ __('messages.subject') }}</th></tr>
                    </thead>
                    <tbody>
                        @forelse($teacher->subjects as $subject)
                        <tr>
                            <td style="color:var(--muted)">{{ $subject->id }}</td>
                            <td style="font-weight:500">{{ $subject->name_ar }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="2" class="text-center py-4" style="color:var(--muted)">{{ __('messages.no_subjects_assigned') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
