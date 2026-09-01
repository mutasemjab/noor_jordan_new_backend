@extends('admin.layouts.app')
@section('title', __('messages.worksheets'))

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-title">{{ __('messages.worksheets') }}</h1>
        <p class="page-sub">{{ __('messages.worksheets_sub') }}</p>
    </div>
    <a href="{{ route('admin.worksheets.create') }}" class="btn-primary-sm">
        <i class="bi bi-plus-circle"></i> {{ __('messages.add_new') }}
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="panel-card">
    <div class="panel-card-body p-0" style="overflow-x:auto">
        <table class="table table-bordered mb-0" style="min-width:580px">
                <thead>
                    <tr>
                        <th class="d-none d-md-table-cell">#</th>
                        <th class="d-none d-sm-table-cell">{{ __('messages.subject') }}</th>
                        <th class="d-none d-sm-table-cell">{{ __('messages.class_label') }}</th>
                        <th>{{ __('messages.title_ar_short') }}</th>
                        <th class="d-none d-lg-table-cell">{{ __('messages.title_en_short') }}</th>
                        <th class="d-none d-md-table-cell">{{ __('messages.year_label') }}</th>
                        <th class="d-none d-md-table-cell">{{ __('messages.pdf_file_label') }}</th>
                        <th>{{ __('messages.Status') }}</th>
                        <th width="150">{{ __('messages.Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($worksheets as $ws)
                    <tr>
                        <td class="d-none d-md-table-cell">{{ $ws->id }}</td>
                        <td class="d-none d-sm-table-cell">{{ $ws->subject?->name_ar ?? '—' }}</td>
                        <td class="d-none d-sm-table-cell">{{ $ws->schoolClass?->name ?? '—' }}</td>
                        <td>
                            {{ $ws->title_ar }}
                            <div class="d-sm-none" style="font-size:.72rem;color:var(--muted)">{{ $ws->subject?->name_ar ?? '' }} — {{ $ws->schoolClass?->name ?? '' }}</div>
                        </td>
                        <td class="d-none d-lg-table-cell">{{ $ws->title_en ?? '—' }}</td>
                        <td class="d-none d-md-table-cell">{{ $ws->year ?? '—' }}</td>
                        <td class="d-none d-md-table-cell">
                            <a href="{{ asset('assets/uploads/worksheets/'.$ws->pdf_file) }}" target="_blank">
                                <i class="bi bi-file-earmark-pdf text-danger"></i> {{ __('messages.view_pdf') }}
                            </a>
                        </td>
                        <td>
                            {!! $ws->status
                                ? '<span class="pill pill-success">'.__('messages.Active').'</span>'
                                : '<span class="pill pill-neutral">'.__('messages.Inactive').'</span>' !!}
                        </td>
                        <td>
                            <a href="{{ route('admin.worksheets.edit', $ws->id) }}" class="btn btn-warning btn-sm">
                                {{ __('messages.Edit') }}
                            </a>
                            <form method="POST" action="{{ route('admin.worksheets.destroy', $ws->id) }}" style="display:inline-block">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm" onclick="return confirm('{{ __('messages.delete_confirm') }}')">
                                    {{ __('messages.Delete') }}
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="text-center py-4" style="color:var(--muted)">{{ __('messages.no_records') }}</td></tr>
                    @endforelse
                </tbody>
        </table>
        <div class="p-3">{{ $worksheets->links() }}</div>
    </div>
</div>

@endsection
