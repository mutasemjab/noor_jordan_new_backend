@extends('teacher.layouts.app')
@section('title', __('messages.worksheets'))

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-title">{{ __('messages.worksheets') }}</h1>
        <p class="page-sub">{{ __('messages.worksheets_sub') }}</p>
    </div>
    <a href="{{ route('teacher.worksheets.create') }}" class="btn-primary-sm">
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
                        <th>{{ __('messages.subject') }}</th>
                        <th>{{ __('messages.title_ar_short') }}</th>
                        <th>{{ __('messages.year_label') }}</th>
                        <th>{{ __('messages.pdf_file_label') }}</th>
                        <th>{{ __('messages.Status') }}</th>
                        <th width="150">{{ __('messages.Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($worksheets as $ws)
                    <tr>
                        <td>{{ $ws->id }}</td>
                        <td>{{ $ws->subject?->name_ar ?? '—' }}</td>
                        <td>{{ $ws->title_ar }}</td>
                        <td>{{ $ws->year ?? '—' }}</td>
                        <td>
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
                            <a href="{{ route('teacher.worksheets.edit', $ws->id) }}" class="btn btn-warning btn-sm">
                                {{ __('messages.Edit') }}
                            </a>
                            <form method="POST" action="{{ route('teacher.worksheets.destroy', $ws->id) }}" style="display:inline-block">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm" onclick="return confirm('{{ __('messages.delete_confirm') }}')">
                                    {{ __('messages.Delete') }}
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-4" style="color:var(--muted)">{{ __('messages.no_records') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $worksheets->links() }}</div>
    </div>
</div>

@endsection
