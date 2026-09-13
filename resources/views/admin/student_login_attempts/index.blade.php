@extends('admin.layouts.app')
@section('title', __('messages.student_login_attempts'))

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-title">{{ __('messages.student_login_attempts') }}</h1>
        <p class="page-sub">{{ __('messages.student_login_attempts_sub') }}</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="panel-card">
    <div class="panel-card-body p-0">
        <div style="overflow-x:auto">
            <table class="table table-bordered mb-0" style="min-width:600px">
                <thead>
                    <tr>
                        <th>{{ __('messages.national_id') }}</th>
                        <th>{{ __('messages.student_name') }}</th>
                        <th>{{ __('messages.failed_attempts_count') }}</th>
                        <th>{{ __('messages.last_attempt_at') }}</th>
                        <th width="120">{{ __('messages.Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $row)
                    <tr>
                        <td>{{ $row->national_id }}</td>
                        <td>{{ $row->student?->name ?? '—' }}</td>
                        <td><span class="pill pill-danger">{{ $row->attempts }}</span></td>
                        <td style="white-space:nowrap;font-size:.82rem">{{ \Illuminate\Support\Carbon::parse($row->last_attempt_at)->format('Y-m-d H:i') }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.student-login-attempts.clear', $row->national_id) }}" style="display:inline-block">
                                @csrf @method('DELETE')
                                <button class="btn btn-outline-secondary btn-sm" onclick="return confirm('{{ __('messages.delete_confirm') }}')">
                                    {{ __('messages.clear') }}
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-4" style="color:var(--muted)">{{ __('messages.no_records') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
