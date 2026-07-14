@extends('teacher.layouts.app')
@section('title', __('messages.t_create_exam'))

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div><h1 class="page-title">{{ __('messages.t_create_exam') }}</h1></div>
    <a href="{{ route('teacher.exams.index') }}" class="btn-outline-sm"><i class="bi bi-arrow-left"></i> {{ __('messages.t_back') }}</a>
</div>

@if($errors->any())
    <div class="alert alert-danger mb-3"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<div class="row g-3">
<div class="col-12 col-xl-8">
<form action="{{ route('teacher.exams.store') }}" method="POST">
@csrf
<div class="panel-card">
    <div class="panel-card-header"><h2 class="panel-card-title">{{ __('messages.t_exam_info') }}</h2></div>
    <div class="panel-card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">{{ __('messages.t_title_ar') }} <span class="text-danger">*</span></label>
                <input type="text" name="title_ar" value="{{ old('title_ar') }}" class="form-control @error('title_ar') is-invalid @enderror" dir="rtl" required>
                @error('title_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ __('messages.t_title_en') }} <span class="text-danger">*</span></label>
                <input type="text" name="title_en" value="{{ old('title_en') }}" class="form-control @error('title_en') is-invalid @enderror" required>
                @error('title_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ __('messages.t_description_ar') }}</label>
                <textarea name="description_ar" rows="2" class="form-control" dir="rtl">{{ old('description_ar') }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ __('messages.t_description_en') }}</label>
                <textarea name="description_en" rows="2" class="form-control">{{ old('description_en') }}</textarea>
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ __('messages.t_exam_type') }} <span class="text-danger">*</span></label>
                <select name="exam_type" class="form-select @error('exam_type') is-invalid @enderror" required>
                    @foreach(['mock','unit','final','practice','previous_years','placement'] as $type)
                        <option value="{{ $type }}" @selected(old('exam_type') === $type)>{{ ucfirst(str_replace('_',' ',$type)) }}</option>
                    @endforeach
                </select>
                @error('exam_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ __('messages.t_difficulty') }}</label>
                <select name="difficulty_level" class="form-select">
                    @foreach(['easy','medium','hard','mixed'] as $d)
                        <option value="{{ $d }}" @selected(old('difficulty_level','mixed') === $d)>{{ ucfirst($d) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ __('messages.t_course') }}</label>
                <select name="course_id" class="form-select">
                    <option value="">{{ __('messages.t_standalone') }}</option>
                    @foreach($courses as $c)
                        <option value="{{ $c->id }}" @selected(old('course_id', request('course_id')) == $c->id)>{{ Str::limit($c->title_en ?: $c->title_ar, 30) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ __('messages.subject') }}</label>
                <select name="subject_id" class="form-select">
                    <option value="">— {{ __('messages.t_none') }} —</option>
                    @foreach($subjects as $sub)
                        <option value="{{ $sub->id }}" @selected(old('subject_id') == $sub->id)>{{ $sub->full_path }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ __('messages.t_duration_minutes') }} <span class="text-danger">*</span></label>
                <input type="number" name="duration_minutes" value="{{ old('duration_minutes', 60) }}" min="1" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ __('messages.t_total_marks') }} <span class="text-danger">*</span></label>
                <input type="number" name="total_marks" value="{{ old('total_marks', 100) }}" min="1" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ __('messages.t_pass_marks') }} <span class="text-danger">*</span></label>
                <input type="number" name="pass_marks" value="{{ old('pass_marks', 50) }}" min="1" class="form-control" required>
            </div>
            <div class="col-12">
                <div class="d-flex gap-4 flex-wrap">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_published" value="1" id="is_published">
                        <label class="form-check-label" for="is_published">{{ __('messages.t_published') }}</label>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="shuffle_questions" value="1" id="shuffle_q">
                        <label class="form-check-label" for="shuffle_q">{{ __('messages.t_shuffle_questions') }}</label>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="shuffle_options" value="1" id="shuffle_o">
                        <label class="form-check-label" for="shuffle_o">{{ __('messages.t_shuffle_options') }}</label>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="show_result_immediately" value="1" id="show_result" checked>
                        <label class="form-check-label" for="show_result">{{ __('messages.t_show_result_immediately') }}</label>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <button type="submit" class="btn-primary-sm"><i class="bi bi-save"></i> {{ __('messages.t_create_exam_add_questions') }}</button>
            </div>
        </div>
    </div>
</div>
</form>
</div>
</div>
@endsection
