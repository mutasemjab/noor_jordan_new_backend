@extends('admin.layouts.app')
@section('title', $student->name)

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-title">{{ $student->name }}</h1>
        <p class="page-sub">{{ $student->email }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.students.edit', $student->id) }}" class="btn-outline-sm"><i class="bi bi-pencil"></i> {{ __('messages.Edit') }}</a>
        <a href="{{ route('admin.students.index') }}" class="btn-outline-sm"><i class="bi bi-arrow-left"></i> {{ __('messages.Back') }}</a>
    </div>
</div>

<div class="row g-3">

    {{-- Profile Sidebar --}}
    <div class="col-12 col-xl-4">
        <div class="panel-card mb-3">
            <div class="panel-card-body text-center">
                @if($student->avatar)
                    <img src="{{ asset('assets/uploads/students/'.$student->avatar) }}" class="rounded-circle mb-3" style="width:90px;height:90px;object-fit:cover">
                @else
                    <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width:90px;height:90px;background:linear-gradient(135deg,#7c3aed,#6d28d9)">
                        <span style="color:#fff;font-size:1.8rem;font-weight:700">{{ strtoupper(substr($student->name, 0, 1)) }}</span>
                    </div>
                @endif
                <h3 style="font-size:1rem;font-weight:700;margin-bottom:4px">{{ $student->name }}</h3>
                <p style="font-size:.82rem;color:var(--muted);margin-bottom:12px">{{ $student->email }}</p>
                <div class="d-flex justify-content-center gap-2">
                    <span class="pill {{ $student->is_active ? 'pill-success' : 'pill-neutral' }}">{{ $student->is_active ? __('messages.Active') : __('messages.Inactive') }}</span>
                </div>
            </div>
        </div>

        <div class="panel-card">
            <div class="panel-card-header"><h2 class="panel-card-title">{{ __('messages.info') }}</h2></div>
            <div class="panel-card-body">
                <div style="font-size:.85rem">
                    @if($student->phone)<div class="d-flex justify-content-between mb-2"><span style="color:var(--muted)">{{ __('messages.phone_label') }}</span><span>{{ $student->phone }}</span></div>@endif
                    @if($student->gender)<div class="d-flex justify-content-between mb-2"><span style="color:var(--muted)">{{ __('messages.gender_label') }}</span><span>{{ $student->gender === 'male' ? __('messages.male') : __('messages.female') }}</span></div>@endif
                    @if($student->nationality)<div class="d-flex justify-content-between mb-2"><span style="color:var(--muted)">{{ __('messages.nationality') }}</span><span>{{ $student->nationality }}</span></div>@endif
                    <div class="d-flex justify-content-between"><span style="color:var(--muted)">{{ __('messages.joined') }}</span><span>{{ $student->created_at->format('M d, Y') }}</span></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Activity --}}
    <div class="col-12 col-xl-8">

        {{-- Enrolled Courses --}}
        <div class="panel-card mb-3">
            <div class="panel-card-header">
                <h2 class="panel-card-title">{{ __('messages.enrolled_courses') }} ({{ $student->enrollments->count() }})</h2>
            </div>
            <div class="panel-card-body p-0">
                <table class="data-table">
                    <thead>
                        <tr><th>{{ __('messages.course') }}</th><th>{{ __('messages.progress') }}</th><th>{{ __('messages.joined') }}</th><th>{{ __('messages.Status') }}</th></tr>
                    </thead>
                    <tbody>
                        @forelse($student->enrollments as $enrollment)
                        <tr>
                            <td>
                                <div style="font-size:.87rem;font-weight:500">{{ Str::limit($enrollment->course->title_en ?: $enrollment->course->title_ar, 40) }}</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div style="flex:1;height:6px;background:#e5e7eb;border-radius:3px;overflow:hidden">
                                        <div style="height:100%;width:{{ $enrollment->progress_percentage }}%;background:var(--primary);border-radius:3px"></div>
                                    </div>
                                    <span style="font-size:.75rem;color:var(--muted);white-space:nowrap">{{ $enrollment->progress_percentage }}%</span>
                                </div>
                            </td>
                            <td style="font-size:.8rem;color:var(--muted)">{{ $enrollment->created_at->format('M d, Y') }}</td>
                            <td><span class="pill {{ $enrollment->is_completed ? 'pill-success' : 'pill-info' }}">{{ $enrollment->is_completed ? __('messages.done') : __('messages.Active') }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center py-3" style="color:var(--muted)">{{ __('messages.no_enrollments_yet') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Exam Attempts --}}
        <div class="panel-card">
            <div class="panel-card-header">
                <h2 class="panel-card-title">{{ __('messages.exam_attempts') }} ({{ $student->examAttempts->count() }})</h2>
            </div>
            <div class="panel-card-body p-0">
                <table class="data-table">
                    <thead>
                        <tr><th>{{ __('messages.exam') }}</th><th>{{ __('messages.score') }}</th><th>%</th><th>{{ __('messages.result') }}</th><th>{{ __('messages.date') }}</th></tr>
                    </thead>
                    <tbody>
                        @forelse($student->examAttempts->take(10) as $attempt)
                        <tr>
                            <td style="font-size:.85rem">{{ Str::limit($attempt->exam->title_en ?: $attempt->exam->title_ar, 35) }}</td>
                            <td style="font-size:.85rem">{{ $attempt->score }}/{{ $attempt->total_marks }}</td>
                            <td style="font-size:.85rem">{{ $attempt->percentage }}%</td>
                            <td><span class="pill {{ $attempt->is_passed ? 'pill-success' : 'pill-warning' }}">{{ $attempt->is_passed ? __('messages.passed') : __('messages.failed') }}</span></td>
                            <td style="font-size:.8rem;color:var(--muted)">{{ $attempt->submitted_at?->format('M d, Y') ?? '—' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center py-3" style="color:var(--muted)">{{ __('messages.no_attempts') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
@endsection
