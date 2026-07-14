@extends('admin.layouts.app')
@section('title', $course->title_en ?: $course->title_ar)

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-title">{{ $course->title_en ?: $course->title_ar }}</h1>
        <p class="page-sub">{{ $course->title_ar }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.courses.edit', $course->id) }}" class="btn-primary-sm">
            <i class="bi bi-pencil"></i> {{ __('messages.Edit') }}
        </a>
        <a href="{{ route('admin.courses.index') }}" class="btn-outline-sm">
            <i class="bi bi-arrow-left"></i> {{ __('messages.Back') }}
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-3">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

{{-- Tabs --}}
<ul class="nav nav-tabs mb-3" id="courseTabs">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-content">📚 {{ __('messages.course_content') }}</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-overview">📋 {{ __('messages.overview') }}</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-add-unit">➕ {{ __('messages.add_unit') }}</a></li>
</ul>

<div class="tab-content">

    {{-- ── TAB: Content ────────────────────────────────────────────── --}}
    <div class="tab-pane fade show active" id="tab-content">
        @forelse($course->units as $unit)
        <div class="panel-card mb-3">
            <div class="panel-card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-collection" style="color:var(--primary)"></i>
                    <strong>{{ __('messages.unit_label') }} {{ $loop->iteration }}: {{ $unit->title_en ?: $unit->title_ar }}</strong>
                    <span class="pill pill-neutral">{{ $unit->lessons->count() }} {{ __('messages.lessons_suffix') }}</span>
                </div>
                <form action="{{ route('admin.courses.units.destroy', $unit->id) }}" method="POST"
                      onsubmit="return confirm('{{ __('messages.delete_unit_confirm') }}')">
                    @csrf @method('DELETE')
                    <button class="btn-outline-sm" style="padding:4px 8px;color:#dc2626;border-color:#fecaca">
                        <i class="bi bi-trash"></i> {{ __('messages.delete_unit') }}
                    </button>
                </form>
            </div>
            <div class="panel-card-body">

                {{-- Lessons list --}}
                @forelse($unit->lessons as $lesson)
                <div class="mb-2">
                    <div class="d-flex align-items-center gap-2 p-2" style="background:var(--bg-soft);border-radius:8px">
                        @if($lesson->lesson_type === 'pdf')
                            <i class="bi bi-file-earmark-pdf-fill" style="color:#dc2626;flex-shrink:0"></i>
                        @else
                            <i class="bi bi-play-circle-fill" style="color:var(--primary);flex-shrink:0"></i>
                        @endif
                        <div style="flex:1;min-width:0">
                            <div style="font-size:.85rem;font-weight:500;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                                {{ $lesson->title_ar ?: $lesson->title_en }}
                                @if($lesson->title_en && $lesson->title_ar)
                                    <span style="color:var(--muted);font-weight:400"> / {{ $lesson->title_en }}</span>
                                @endif
                            </div>
                            <div style="font-size:.75rem;color:var(--muted);display:flex;gap:6px;align-items:center;flex-wrap:wrap">
                                {{ $lesson->duration_minutes ? $lesson->duration_minutes.' '.__('messages.min_suffix') : '' }}
                                @if($lesson->is_free)<span class="pill pill-success" style="font-size:.6rem">{{ __('messages.free') }}</span>@endif
                                @if($lesson->lesson_type === 'pdf' && $lesson->file_path)
                                    <a href="{{ asset('assets/uploads/lessons/'.$lesson->file_path) }}" target="_blank" style="font-size:.7rem;color:#dc2626"><i class="bi bi-download"></i> PDF</a>
                                @elseif($lesson->video_url)
                                    <a href="{{ $lesson->video_url }}" target="_blank" style="font-size:.7rem;color:var(--primary)"><i class="bi bi-play-btn"></i> {{ mb_strlen($lesson->video_url) > 40 ? mb_substr($lesson->video_url, 0, 40).'…' : $lesson->video_url }}</a>
                                @endif
                            </div>
                        </div>
                        {{-- Edit button --}}
                        <button class="btn-outline-sm" type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#edit-lesson-{{ $lesson->id }}"
                                style="padding:2px 6px;flex-shrink:0">
                            <i class="bi bi-pencil"></i>
                        </button>
                        {{-- Delete button --}}
                        <form action="{{ route('admin.courses.lessons.destroy', $lesson->id) }}" method="POST"
                              onsubmit="return confirm('{{ __('messages.delete_lesson_confirm') }}')">
                            @csrf @method('DELETE')
                            <button class="btn-outline-sm" style="padding:2px 6px;color:#dc2626;border-color:#fecaca;flex-shrink:0">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                    {{-- Edit collapse form --}}
                    <div class="collapse mt-1" id="edit-lesson-{{ $lesson->id }}">
                        <form action="{{ route('admin.courses.lessons.update', $lesson->id) }}" method="POST"
                              enctype="multipart/form-data"
                              style="background:#f0f7ff;border-radius:10px;padding:14px;border:1px solid #bfdbfe">
                            @csrf @method('PUT')
                            {{-- Type toggle --}}
                            <div class="d-flex gap-3 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="lesson_type"
                                           id="elt-video-{{ $lesson->id }}" value="video"
                                           {{ $lesson->lesson_type === 'video' ? 'checked' : '' }}
                                           onchange="toggleEditLessonType({{ $lesson->id }})">
                                    <label class="form-check-label" for="elt-video-{{ $lesson->id }}" style="font-size:.82rem">
                                        <i class="bi bi-play-circle"></i> {{ __('messages.video_type') }}
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="lesson_type"
                                           id="elt-pdf-{{ $lesson->id }}" value="pdf"
                                           {{ $lesson->lesson_type === 'pdf' ? 'checked' : '' }}
                                           onchange="toggleEditLessonType({{ $lesson->id }})">
                                    <label class="form-check-label" for="elt-pdf-{{ $lesson->id }}" style="font-size:.82rem">
                                        <i class="bi bi-file-earmark-pdf"></i> {{ __('messages.pdf_type') }}
                                    </label>
                                </div>
                            </div>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <input type="text" name="title_ar" class="form-control form-control-sm"
                                           placeholder="{{ __('messages.lesson_title_ar') }}" dir="rtl"
                                           value="{{ $lesson->title_ar }}" required>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" name="title_en" class="form-control form-control-sm"
                                           placeholder="{{ __('messages.lesson_title_en') }}"
                                           value="{{ $lesson->title_en }}">
                                </div>
                                {{-- Video URL --}}
                                <div class="col-12" id="evideo-wrap-{{ $lesson->id }}"
                                     style="{{ $lesson->lesson_type === 'pdf' ? 'display:none' : '' }}">
                                    <input type="url" name="video_url" class="form-control form-control-sm"
                                           placeholder="{{ __('messages.video_url_ph') }}"
                                           value="{{ $lesson->video_url }}">
                                </div>
                                {{-- PDF upload --}}
                                <div class="col-12" id="epdf-wrap-{{ $lesson->id }}"
                                     style="{{ $lesson->lesson_type === 'video' ? 'display:none' : '' }}">
                                    @if($lesson->file_path)
                                        <div class="mb-1" style="font-size:.78rem;color:var(--muted)">
                                            {{ __('messages.current_file') }}:
                                            <a href="{{ asset('assets/uploads/lessons/'.$lesson->file_path) }}" target="_blank" style="color:#dc2626">
                                                <i class="bi bi-file-earmark-pdf"></i> {{ $lesson->file_path }}
                                            </a>
                                        </div>
                                    @endif
                                    <input type="file" name="lesson_file" class="form-control form-control-sm" accept=".pdf">
                                    <small class="text-muted" style="font-size:.72rem">{{ __('messages.leave_empty_keep_file') }}</small>
                                </div>
                                <div class="col-md-6">
                                    <input type="number" name="duration_minutes" class="form-control form-control-sm"
                                           placeholder="{{ __('messages.duration_min') }}" min="1"
                                           value="{{ $lesson->duration_minutes }}">
                                </div>
                                <div class="col-md-6 d-flex align-items-end">
                                    <div class="form-check form-check-inline mb-0">
                                        <input class="form-check-input" type="checkbox" name="is_free" value="1"
                                               id="elf-{{ $lesson->id }}" {{ $lesson->is_free ? 'checked' : '' }}>
                                        <label class="form-check-label" for="elf-{{ $lesson->id }}" style="font-size:.82rem">{{ __('messages.free_preview') }}</label>
                                    </div>
                                </div>
                                <div class="col-12 d-flex gap-2">
                                    <button type="submit" class="btn-primary-sm">
                                        <i class="bi bi-save"></i> {{ __('messages.Save') }}
                                    </button>
                                    <button type="button" class="btn-outline-sm"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#edit-lesson-{{ $lesson->id }}">
                                        {{ __('messages.Cancel') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                @empty
                <p style="color:var(--muted);font-size:.83rem">{{ __('messages.no_lessons_yet') }}</p>
                @endforelse

                {{-- Add lesson --}}
                <button class="btn-outline-sm mt-2" type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#add-lesson-{{ $unit->id }}">
                    <i class="bi bi-plus"></i> {{ __('messages.add_lesson') }}
                </button>
                <div class="collapse mt-2" id="add-lesson-{{ $unit->id }}">
                    <form action="{{ route('admin.courses.lessons.store', $unit->id) }}" method="POST"
                          enctype="multipart/form-data"
                          style="background:#f8fafc;border-radius:10px;padding:14px;border:1px solid #e2e8f0">
                        @csrf
                        {{-- Type toggle --}}
                        <div class="d-flex gap-3 mb-2">
                            <div class="form-check">
                                <input class="form-check-input lesson-type-radio" type="radio" name="lesson_type"
                                       id="lt-video-{{ $unit->id }}" value="video" checked
                                       onchange="toggleLessonType({{ $unit->id }})">
                                <label class="form-check-label" for="lt-video-{{ $unit->id }}" style="font-size:.82rem">
                                    <i class="bi bi-play-circle"></i> {{ __('messages.video_type') }}
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input lesson-type-radio" type="radio" name="lesson_type"
                                       id="lt-pdf-{{ $unit->id }}" value="pdf"
                                       onchange="toggleLessonType({{ $unit->id }})">
                                <label class="form-check-label" for="lt-pdf-{{ $unit->id }}" style="font-size:.82rem">
                                    <i class="bi bi-file-earmark-pdf"></i> {{ __('messages.pdf_type') }}
                                </label>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <input type="text" name="title_ar" class="form-control form-control-sm" placeholder="{{ __('messages.lesson_title_ar') }}" dir="rtl" required>
                            </div>
                            <div class="col-md-6">
                                <input type="text" name="title_en" class="form-control form-control-sm" placeholder="{{ __('messages.lesson_title_en') }}">
                            </div>
                            {{-- Video URL (shown for video type) --}}
                            <div class="col-12" id="video-url-wrap-{{ $unit->id }}">
                                <input type="url" name="video_url" class="form-control form-control-sm" placeholder="{{ __('messages.video_url_ph') }}">
                            </div>
                            {{-- PDF upload (shown for pdf type) --}}
                            <div class="col-12" id="pdf-file-wrap-{{ $unit->id }}" style="display:none">
                                <input type="file" name="lesson_file" class="form-control form-control-sm" accept=".pdf">
                            </div>
                            <div class="col-md-6">
                                <input type="number" name="duration_minutes" class="form-control form-control-sm" placeholder="{{ __('messages.duration_min') }}" min="1">
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <div class="form-check form-check-inline mb-0">
                                    <input class="form-check-input" type="checkbox" name="is_free" value="1" id="lf-{{ $unit->id }}">
                                    <label class="form-check-label" for="lf-{{ $unit->id }}" style="font-size:.82rem">{{ __('messages.free_preview') }}</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn-primary-sm">
                                    <i class="bi bi-save"></i> {{ __('messages.add_lesson') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
        @empty
        <div class="panel-card">
            <div class="panel-card-body text-center py-5">
                <div style="font-size:48px;margin-bottom:12px">📂</div>
                <p style="color:var(--muted)">{{ __('messages.no_units_yet_add') }}</p>
                <button class="btn-primary-sm" data-bs-toggle="tab" data-bs-target="#tab-add-unit">
                    <i class="bi bi-plus-circle"></i> {{ __('messages.add_first_unit') }}
                </button>
            </div>
        </div>
        @endforelse
    </div>

    {{-- ── TAB: Overview ───────────────────────────────────────────── --}}
    <div class="tab-pane fade" id="tab-overview">
        <div class="row g-3">
            <div class="col-12 col-xl-8">
                @if($course->thumbnail)
                <div class="panel-card mb-3">
                    <div class="panel-card-body p-0">
                        <img src="{{ asset('assets/uploads/courses/' . $course->thumbnail) }}" class="img-fluid rounded" alt="" style="max-height:260px;object-fit:cover;width:100%">
                    </div>
                </div>
                @endif
                <div class="panel-card">
                    <div class="panel-card-header"><h2 class="panel-card-title">{{ __('messages.descriptions') }}</h2></div>
                    <div class="panel-card-body">
                        <div class="mb-3">
                            <div style="font-size:.75rem;font-weight:700;text-transform:uppercase;color:var(--muted);margin-bottom:6px">AR</div>
                            <p style="line-height:1.7" dir="rtl">{{ $course->description_ar ?: '—' }}</p>
                        </div>
                        <div>
                            <div style="font-size:.75rem;font-weight:700;text-transform:uppercase;color:var(--muted);margin-bottom:6px">EN</div>
                            <p style="line-height:1.7">{{ $course->description_en ?: '—' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-xl-4">
                <div class="panel-card mb-3">
                    <div class="panel-card-header"><h2 class="panel-card-title">{{ __('messages.overview') }}</h2></div>
                    <div class="panel-card-body">
                        @php
                            $details = [
                                ['bi-person-workspace', __('messages.teacher'), $course->teacher->name ?? '—'],
                                ['bi-tag', __('messages.category'), $course->category->name_en ?? $course->category->name_ar ?? '—'],
                                ['bi-currency-dollar', __('messages.price'), $course->is_free ? __('messages.free') : number_format($course->price).' JD'],
                                ['bi-people', __('messages.students'), number_format($course->enrollments_count ?? 0)],
                                ['bi-clock', __('messages.duration_hours'), ($course->duration_hours ?? 0).' h'],
                                ['bi-bar-chart', __('messages.level'), $course->difficulty_level ?? '—'],
                            ];
                        @endphp
                        @foreach($details as $d)
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi {{ $d[0] }}" style="color:var(--primary);width:18px"></i>
                            <span style="color:var(--muted);font-size:.83rem;min-width:80px">{{ $d[1] }}</span>
                            <span style="font-size:.85rem;font-weight:500">{{ $d[2] }}</span>
                        </div>
                        @endforeach
                        <hr>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="pill {{ $course->is_published ? 'pill-success' : 'pill-neutral' }}">
                                {{ $course->is_published ? __('messages.published') : __('messages.draft') }}
                            </span>
                            @if($course->is_featured)<span class="pill pill-warning">{{ __('messages.featured') }}</span>@endif
                            @if($course->is_free)<span class="pill pill-success">{{ __('messages.free') }}</span>@endif
                        </div>
                    </div>
                </div>
                <div class="panel-card">
                    <div class="panel-card-header"><h2 class="panel-card-title">{{ __('messages.danger_zone') }}</h2></div>
                    <div class="panel-card-body">
                        <form action="{{ route('admin.courses.destroy', $course->id) }}" method="POST"
                              onsubmit="return confirm('{{ __('messages.delete_course_permanently_confirm') }}')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-outline-sm w-100 justify-content-center" style="color:#dc2626;border-color:#fecaca;padding:10px">
                                <i class="bi bi-trash"></i> {{ __('messages.delete_course') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── TAB: Add Unit ───────────────────────────────────────────── --}}
    <div class="tab-pane fade" id="tab-add-unit">
        <div class="row g-3">
        <div class="col-12 col-xl-6">
        <div class="panel-card">
            <div class="panel-card-header"><h2 class="panel-card-title">{{ __('messages.add_unit') }}</h2></div>
            <div class="panel-card-body">
                @if($errors->has('title_ar'))
                    <div class="alert alert-danger">{{ $errors->first('title_ar') }}</div>
                @endif
                <form action="{{ route('admin.courses.units.store', $course->id) }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.unit_title_ar') }} <span class="text-danger">*</span></label>
                            <input type="text" name="title_ar" value="{{ old('title_ar') }}" class="form-control" dir="rtl" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.unit_title_en') }}</label>
                            <input type="text" name="title_en" value="{{ old('title_en') }}" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('messages.order_index') }}</label>
                            <input type="number" name="order_index" value="{{ old('order_index', $course->units->count() + 1) }}" min="0" class="form-control">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn-primary-sm">
                                <i class="bi bi-plus-circle"></i> {{ __('messages.add_unit') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        </div>
        </div>
    </div>

</div>
@push('scripts')
<script>
function toggleLessonType(unitId) {
    const selected = document.querySelector(`input[name="lesson_type"][id^="lt-"][id$="-${unitId}"]:checked`).value;
    document.getElementById(`video-url-wrap-${unitId}`).style.display = (selected === 'video') ? '' : 'none';
    document.getElementById(`pdf-file-wrap-${unitId}`).style.display  = (selected === 'pdf')   ? '' : 'none';
}
function toggleEditLessonType(lessonId) {
    const selected = document.querySelector(`input[name="lesson_type"][id^="elt-"][id$="-${lessonId}"]:checked`).value;
    document.getElementById(`evideo-wrap-${lessonId}`).style.display = (selected === 'video') ? '' : 'none';
    document.getElementById(`epdf-wrap-${lessonId}`).style.display   = (selected === 'pdf')   ? '' : 'none';
}
</script>
@endpush
@endsection
