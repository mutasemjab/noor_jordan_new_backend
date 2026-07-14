@extends('teacher.layouts.app')
@section('title', __('messages.t_edit_course'))

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div><h1 class="page-title">{{ __('messages.t_edit_course') }}</h1></div>
    <div class="d-flex gap-2">
        <a href="{{ route('teacher.courses.show', $course->id) }}" class="btn-outline-sm"><i class="bi bi-layout-text-sidebar"></i> {{ __('messages.t_manage') }}</a>
        <a href="{{ route('teacher.courses.index') }}" class="btn-outline-sm"><i class="bi bi-arrow-left"></i> {{ __('messages.t_back') }}</a>
    </div>
</div>

@if($errors->any())
    <div class="alert alert-danger mb-3"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<form action="{{ route('teacher.courses.update', $course->id) }}" method="POST" enctype="multipart/form-data">
@csrf @method('PUT')
<div class="row g-3">

    <div class="col-12 col-xl-8">
        <div class="panel-card">
            <div class="panel-card-header"><h2 class="panel-card-title">{{ __('messages.t_course_details') }}</h2></div>
            <div class="panel-card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.t_title_ar') }} <span class="text-danger">*</span></label>
                        <input type="text" name="title_ar" value="{{ old('title_ar', $course->title_ar) }}" class="form-control @error('title_ar') is-invalid @enderror" dir="rtl" required>
                        @error('title_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.t_title_en') }} <span class="text-danger">*</span></label>
                        <input type="text" name="title_en" value="{{ old('title_en', $course->title_en) }}" class="form-control @error('title_en') is-invalid @enderror" required>
                        @error('title_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.t_description_ar') }}</label>
                        <textarea name="description_ar" rows="3" class="form-control" dir="rtl">{{ old('description_ar', $course->description_ar) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.t_description_en') }}</label>
                        <textarea name="description_en" rows="3" class="form-control">{{ old('description_en', $course->description_en) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.t_what_you_learn_ar') }}</label>
                        <textarea name="what_you_learn_ar" rows="3" class="form-control" dir="rtl">{{ old('what_you_learn_ar', $course->what_you_learn_ar) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.t_what_you_learn_en') }}</label>
                        <textarea name="what_you_learn_en" rows="3" class="form-control">{{ old('what_you_learn_en', $course->what_you_learn_en) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.t_requirements_ar') }}</label>
                        <textarea name="requirements_ar" rows="2" class="form-control" dir="rtl">{{ old('requirements_ar', $course->requirements_ar) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.t_requirements_en') }}</label>
                        <textarea name="requirements_en" rows="2" class="form-control">{{ old('requirements_en', $course->requirements_en) }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-4">
        <div class="panel-card mb-3">
            <div class="panel-card-header"><h2 class="panel-card-title">{{ __('messages.t_settings') }}</h2></div>
            <div class="panel-card-body">
                <div class="mb-3">
                    <label class="form-label">{{ __('messages.t_category') }} <span class="text-danger">*</span></label>
                    <select name="category_id" class="form-select" required>
                        <option value="">{{ __('messages.t_select') }}</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" @selected(old('category_id', $course->category_id) == $cat->id)>{{ $cat->full_path }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('messages.t_subject') }}</label>
                    <select name="subject_id" class="form-select">
                        <option value="">{{ __('messages.t_none') }}</option>
                        @foreach($subjects as $sub)
                            <option value="{{ $sub->id }}" @selected(old('subject_id', $course->subject_id) == $sub->id)>{{ $sub->full_path }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('messages.t_difficulty') }}</label>
                    <select name="difficulty_level" class="form-select">
                        @foreach(['beginner','intermediate','advanced'] as $l)
                            <option value="{{ $l }}" @selected(old('difficulty_level', $course->difficulty_level) === $l)>{{ ucfirst($l) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label">{{ __('messages.t_price') }}</label>
                        <input type="number" name="price" value="{{ old('price', $course->price) }}" min="0" step="0.01" class="form-control">
                    </div>
                    <div class="col-6">
                        <label class="form-label">{{ __('messages.t_old_price') }}</label>
                        <input type="number" name="old_price" value="{{ old('old_price', $course->old_price) }}" min="0" step="0.01" class="form-control">
                    </div>
                    <div class="col-12">
                        <label class="form-label">{{ __('messages.t_duration_hours') }}</label>
                        <input type="number" name="duration_hours" value="{{ old('duration_hours', $course->duration_hours) }}" min="0" class="form-control">
                    </div>
                </div>
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" name="is_published" value="1" id="is_published" @checked($course->is_published)>
                    <label class="form-check-label" for="is_published">{{ __('messages.t_published') }}</label>
                </div>
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" name="is_free" value="1" id="is_free" @checked($course->is_free)>
                    <label class="form-check-label" for="is_free">{{ __('messages.t_free_course') }}</label>
                </div>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" name="sequential_videos" value="1" id="sequential_videos" @checked($course->sequential_videos)>
                    <label class="form-check-label" for="sequential_videos">
                        {{ __('messages.t_sequential_videos') }}
                        <small class="d-block text-muted" style="font-size:.75rem">{{ __('messages.t_sequential_videos_hint') }}</small>
                    </label>
                </div>
                @if($course->thumbnail)
                <div class="mb-2">
                    <img src="{{ asset('uploads/courses/'.$course->thumbnail) }}" style="width:100%;height:120px;object-fit:cover;border-radius:8px">
                </div>
                @endif
                <label class="form-label">{{ __('messages.t_thumbnail') }}</label>
                <input type="file" name="thumbnail" accept="image/*" class="form-control mb-3">
                <button type="submit" class="btn-primary-sm w-100 justify-content-center">
                    <i class="bi bi-save"></i> {{ __('messages.t_save_changes') }}
                </button>
            </div>
        </div>
    </div>

</div>
</form>
@endsection
