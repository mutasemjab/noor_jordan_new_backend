@extends('teacher.layouts.app')
@section('title', __('messages.t_question_banks'))

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-title">{{ __('messages.t_question_banks') }}</h1>
        <p class="page-sub">{{ __('messages.question_banks_sub') }}</p>
    </div>
    <a href="{{ route('teacher.question-banks.create') }}" class="btn-primary-sm">
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
                        <th>{{ __('messages.pdf_file_label') }}</th>
                        <th>{{ __('messages.Status') }}</th>
                        <th width="150">{{ __('messages.Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($questionBanks as $qb)
                    <tr>
                        <td>{{ $qb->id }}</td>
                        <td>{{ $qb->subject?->name_ar ?? '—' }}</td>
                        <td>{{ $qb->title_ar }}</td>
                        <td>
                            <a href="{{ asset('assets/uploads/questionBank/'.$qb->pdf_file) }}" target="_blank">
                                <i class="bi bi-file-earmark-pdf text-danger"></i> {{ __('messages.view_pdf') }}
                            </a>
                        </td>
                        <td>
                            {!! $qb->status
                                ? '<span class="pill pill-success">'.__('messages.Active').'</span>'
                                : '<span class="pill pill-neutral">'.__('messages.Inactive').'</span>' !!}
                        </td>
                        <td>
                            <a href="{{ route('teacher.question-banks.edit', $qb->id) }}" class="btn btn-warning btn-sm">
                                {{ __('messages.Edit') }}
                            </a>
                            <form method="POST" action="{{ route('teacher.question-banks.destroy', $qb->id) }}" style="display:inline-block">
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
        <div class="p-3">{{ $questionBanks->links() }}</div>
    </div>
</div>

@endsection
