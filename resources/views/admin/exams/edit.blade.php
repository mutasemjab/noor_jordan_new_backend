@extends('admin.layouts.app')
@section('title', __('messages.edit_exam'))

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div><h1 class="page-title">{{ __('messages.edit_exam') }}</h1></div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.exams.show', $exam->id) }}" class="btn-outline-sm"><i class="bi bi-list-check"></i> {{ __('messages.questions') }}</a>
        <a href="{{ route('admin.exams.index') }}" class="btn-outline-sm"><i class="bi bi-arrow-left"></i> {{ __('messages.Back') }}</a>
    </div>
</div>

@if($errors->any())
    <div class="alert alert-danger mb-3"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<div class="row g-3">
<div class="col-12 col-xl-8">
<form action="{{ route('admin.exams.update', $exam->id) }}" method="POST">
@csrf @method('PUT')
<div class="panel-card">
    <div class="panel-card-header"><h2 class="panel-card-title">{{ __('messages.exam_info') }}</h2></div>
    <div class="panel-card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">{{ __('messages.title_ar') }} <span class="text-danger">*</span></label>
                <input type="text" name="title_ar" value="{{ old('title_ar', $exam->title_ar) }}" class="form-control @error('title_ar') is-invalid @enderror" dir="rtl" required>
                @error('title_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ __('messages.title_en') }} <span class="text-danger">*</span></label>
                <input type="text" name="title_en" value="{{ old('title_en', $exam->title_en) }}" class="form-control @error('title_en') is-invalid @enderror" required>
                @error('title_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ __('messages.description_ar') }}</label>
                <textarea name="description_ar" rows="2" class="form-control" dir="rtl">{{ old('description_ar', $exam->description_ar) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ __('messages.description_en') }}</label>
                <textarea name="description_en" rows="2" class="form-control">{{ old('description_en', $exam->description_en) }}</textarea>
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ __('messages.exam_type_label') }} <span class="text-danger">*</span></label>
                <select name="exam_type" class="form-select @error('exam_type') is-invalid @enderror" required>
                    @foreach(['mock','unit','final','practice','previous_years','placement'] as $type)
                        <option value="{{ $type }}" @selected(old('exam_type', $exam->exam_type) === $type)>{{ __('messages.'.$type) }}</option>
                    @endforeach
                </select>
                @error('exam_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ __('messages.difficulty') }}</label>
                <select name="difficulty_level" class="form-select">
                    @foreach(['easy','medium','hard','mixed'] as $d)
                        <option value="{{ $d }}" @selected(old('difficulty_level', $exam->difficulty_level) === $d)>{{ __('messages.'.$d) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ __('messages.course') }}</label>
                <select name="course_id" id="courseSelect" class="form-select">
                    <option value="">{{ __('messages.standalone') }}</option>
                    @foreach($courses as $c)
                        <option value="{{ $c->id }}" @selected(old('course_id', $exam->course_id) == $c->id)>{{ Str::limit($c->title_en ?: $c->title_ar, 35) }}</option>
                    @endforeach
                </select>
            </div>

            {{-- ── Course Placement ── --}}
            @php
                $currentPlacement = old('placement_type',
                    $exam->lesson_id ? 'lesson' : ($exam->unit_id ? 'unit' : 'course'));
            @endphp
            <div class="col-12" id="placementPanel" style="{{ $exam->course_id ? '' : 'display:none;' }}">
                <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:16px;">
                    <div class="fw-semibold mb-2" style="font-size:.85rem;color:var(--navy);">
                        <i class="bi bi-pin-map me-1"></i> {{ __('messages.exam_placement') }}
                    </div>
                    <div class="d-flex flex-wrap gap-3 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="placement_type" id="pt_course" value="course" @checked($currentPlacement === 'course') onchange="updatePlacementUI()">
                            <label class="form-check-label" for="pt_course">{{ __('messages.placement_course_level') }}</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="placement_type" id="pt_unit" value="unit" @checked($currentPlacement === 'unit') onchange="updatePlacementUI()">
                            <label class="form-check-label" for="pt_unit">{{ __('messages.placement_after_unit') }}</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="placement_type" id="pt_lesson" value="lesson" @checked($currentPlacement === 'lesson') onchange="updatePlacementUI()">
                            <label class="form-check-label" for="pt_lesson">{{ __('messages.placement_after_lesson') }}</label>
                        </div>
                    </div>
                    <div class="row g-2" id="placementSelects">
                        <div class="col-md-6" id="unitSelectWrap" style="{{ ($currentPlacement === 'unit' || $currentPlacement === 'lesson') ? '' : 'display:none;' }}">
                            <label class="form-label" style="font-size:.8rem;">{{ __('messages.select_unit') }}</label>
                            <select name="unit_id" id="unitSelect" class="form-select form-select-sm" onchange="filterLessons()">
                                <option value="">— {{ __('messages.select_unit') }} —</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="lessonSelectWrap" style="{{ $currentPlacement === 'lesson' ? '' : 'display:none;' }}">
                            <label class="form-label" style="font-size:.8rem;">{{ __('messages.select_lesson') }}</label>
                            <select name="lesson_id" id="lessonSelect" class="form-select form-select-sm">
                                <option value="">— {{ __('messages.select_lesson') }} —</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <label class="form-label">{{ __('messages.duration_minutes_label') }} <span class="text-danger">*</span></label>
                <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $exam->duration_minutes) }}" min="1" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ __('messages.total_marks') }} <span class="text-danger">*</span></label>
                <input type="number" name="total_marks" value="{{ old('total_marks', $exam->total_marks) }}" min="1" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ __('messages.pass_marks') }} <span class="text-danger">*</span></label>
                <input type="number" name="pass_marks" value="{{ old('pass_marks', $exam->pass_marks) }}" min="1" class="form-control" required>
            </div>
            <div class="col-12">
                <div class="d-flex gap-4 flex-wrap">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_published" value="1" id="is_published" @checked($exam->is_published)>
                        <label class="form-check-label" for="is_published">{{ __('messages.published') }}</label>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="shuffle_questions" value="1" id="shuffle_q" @checked($exam->shuffle_questions)>
                        <label class="form-check-label" for="shuffle_q">{{ __('messages.shuffle_questions') }}</label>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="shuffle_options" value="1" id="shuffle_o" @checked($exam->shuffle_options)>
                        <label class="form-check-label" for="shuffle_o">{{ __('messages.shuffle_options') }}</label>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="show_result_immediately" value="1" id="show_result" @checked($exam->show_result_immediately)>
                        <label class="form-check-label" for="show_result">{{ __('messages.show_result_immediately') }}</label>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <button type="submit" class="btn-primary-sm"><i class="bi bi-save"></i> {{ __('messages.save_changes') }}</button>
            </div>
        </div>
    </div>
</div>
</form>
</div>
</div>

@push('scripts')
<script>
function buildStructureUrl(id) {
    return '{{ route("admin.courses.exam-structure", ":cid") }}'.replace(':cid', id);
}

const initUnitId   = '{{ old("unit_id", $exam->unit_id) }}';
const initLessonId = '{{ old("lesson_id", $exam->lesson_id) }}';

document.getElementById('courseSelect').addEventListener('change', function () {
    loadCourseStructure(this.value);
});

window.addEventListener('DOMContentLoaded', function () {
    const courseId = document.getElementById('courseSelect').value;
    if (courseId) {
        loadCourseStructure(courseId, initUnitId, initLessonId);
    }
    updatePlacementUI();
});

function loadCourseStructure(courseId, preselectUnit, preselectLesson) {
    const panel = document.getElementById('placementPanel');
    if (!courseId) {
        panel.style.display = 'none';
        return;
    }
    fetch(buildStructureUrl(courseId))
        .then(r => r.json())
        .then(data => {
            buildUnitSelect(data.units, preselectUnit);
            panel.style.display = '';
            updatePlacementUI();
            if (preselectUnit) filterLessons(preselectLesson);
        });
}

function buildUnitSelect(units, preselectUnit) {
    const sel = document.getElementById('unitSelect');
    sel.innerHTML = '<option value="">— {{ __("messages.select_unit") }} —</option>';
    units.forEach(u => {
        const opt = document.createElement('option');
        opt.value = u.id;
        opt.textContent = (u.title_en || u.title_ar);
        opt.dataset.lessons = JSON.stringify(u.lessons);
        if (preselectUnit && String(u.id) === String(preselectUnit)) opt.selected = true;
        sel.appendChild(opt);
    });
}

function filterLessons(preselectLesson) {
    const unitSel   = document.getElementById('unitSelect');
    const lessonSel = document.getElementById('lessonSelect');
    const selected  = unitSel.options[unitSel.selectedIndex];
    lessonSel.innerHTML = '<option value="">— {{ __("messages.select_lesson") }} —</option>';
    if (!selected || !selected.dataset.lessons) return;
    const lessons = JSON.parse(selected.dataset.lessons);
    lessons.forEach(l => {
        const opt = document.createElement('option');
        opt.value = l.id;
        opt.textContent = (l.title_en || l.title_ar);
        if (preselectLesson && String(l.id) === String(preselectLesson)) opt.selected = true;
        lessonSel.appendChild(opt);
    });
}

function updatePlacementUI() {
    const type = document.querySelector('input[name="placement_type"]:checked')?.value || 'course';
    document.getElementById('unitSelectWrap').style.display   = (type === 'unit' || type === 'lesson') ? '' : 'none';
    document.getElementById('lessonSelectWrap').style.display = (type === 'lesson') ? '' : 'none';
}
</script>
@endpush
@endsection
