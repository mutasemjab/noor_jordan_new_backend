@extends('admin.layouts.app')
@section('title', __('messages.subjects_title'))

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-title">{{ __('messages.subjects_title') }}</h1>
        <p class="page-sub">{{ __('messages.manage_subjects_desc') }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.subjects.create') }}" class="btn-primary-sm">
            <i class="bi bi-plus-circle"></i> {{ __('messages.add_subject') }}
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3">
        {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="panel-card mb-3">
    <div class="panel-card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-12 col-md-5">
                <input type="text" name="search" value="{{ request('search') }}"
                       class="form-control form-control-sm" placeholder="{{ __('messages.search_subject_ph') }}">
            </div>
            <div class="col-12 col-md-2">
                <button type="submit" class="btn-primary-sm w-100"><i class="bi bi-search"></i></button>
            </div>
        </form>
    </div>
</div>

<div class="panel-card">
    <div class="panel-card-body p-0" style="overflow-x:auto">
        <table class="data-table" style="min-width:520px">
            <thead>
                <tr>
                    <th class="d-none d-md-table-cell">#</th>
                    <th>{{ __('messages.subject_ar') }}</th>
                    <th class="d-none d-sm-table-cell">{{ __('messages.subject_en') }}</th>
                    <th class="d-none d-lg-table-cell">{{ __('messages.full_path') }}</th>
                    <th>{{ __('messages.Status') }}</th>
                    <th>{{ __('messages.Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($subjects as $subject)
                <tr>
                    <td class="d-none d-md-table-cell" style="color:var(--muted)">{{ $subject->id }}</td>
                    <td>
                        <div style="font-weight:600" dir="rtl">{{ $subject->name_ar }}</div>
                        <div class="d-sm-none" style="font-size:.72rem;color:var(--muted)">{{ $subject->name_en ?: '' }}</div>
                    </td>
                    <td class="d-none d-sm-table-cell">{{ $subject->name_en ?: '—' }}</td>
                    <td class="d-none d-lg-table-cell" style="color:var(--muted);font-size:.8rem">{{ $subject->full_path }}</td>
                    <td>
                        <span class="pill {{ $subject->is_active ? 'pill-success' : 'pill-neutral' }}">
                            {{ $subject->is_active ? __('messages.Active') : __('messages.Inactive') }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.subjects.edit', $subject->id) }}"
                               class="btn-outline-sm" style="padding:4px 8px">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.subjects.destroy', $subject->id) }}" method="POST"
                                  onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                @csrf @method('DELETE')
                                <button class="btn-outline-sm" style="padding:4px 8px;color:#dc2626;border-color:#fecaca">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center py-4" style="color:var(--muted)">
                        {{ __('messages.no_subjects_yet') }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-3">{{ $subjects->withQueryString()->links() }}</div>
    </div>
</div>

@endsection
