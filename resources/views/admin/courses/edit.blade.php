@extends('admin.layouts.app')
@section('title', __('messages.edit_course'))

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-title">{{ __('messages.edit_course') }}</h1>
        <p class="page-sub">{{ $course->title_en ?: $course->title_ar }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.courses.show', $course->id) }}" class="btn-outline-sm">
            <i class="bi bi-eye"></i> {{ __('messages.View') }}
        </a>
        <a href="{{ route('admin.courses.index') }}" class="btn-outline-sm">
            <i class="bi bi-arrow-left"></i> {{ __('messages.Back') }}
        </a>
    </div>
</div>

@if($errors->any())
    <div class="alert alert-danger mb-3">
        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

<form action="{{ route('admin.courses.update', $course->id) }}" method="POST" enctype="multipart/form-data">
@csrf @method('PUT')

<div class="row g-3">

    <div class="col-12 col-xl-8">
        <div class="panel-card mb-3">
            <div class="panel-card-header"><h2 class="panel-card-title">{{ __('messages.course_info') }}</h2></div>
            <div class="panel-card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.title_ar') }} <span class="text-danger">*</span></label>
                        <input type="text" name="title_ar" value="{{ old('title_ar', $course->title_ar) }}" class="form-control @error('title_ar') is-invalid @enderror" dir="rtl" required>
                        @error('title_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.title_en') }} <span class="text-danger">*</span></label>
                        <input type="text" name="title_en" value="{{ old('title_en', $course->title_en) }}" class="form-control @error('title_en') is-invalid @enderror" required>
                        @error('title_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.description_ar') }}</label>
                        <textarea name="description_ar" rows="3" class="form-control" dir="rtl">{{ old('description_ar', $course->description_ar) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.description_en') }}</label>
                        <textarea name="description_en" rows="3" class="form-control">{{ old('description_en', $course->description_en) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.what_you_learn_ar') }}</label>
                        <textarea name="what_you_learn_ar" rows="3" class="form-control" dir="rtl">{{ old('what_you_learn_ar', $course->what_you_learn_ar) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.what_you_learn_en') }}</label>
                        <textarea name="what_you_learn_en" rows="3" class="form-control">{{ old('what_you_learn_en', $course->what_you_learn_en) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.requirements_ar') }}</label>
                        <textarea name="requirements_ar" rows="2" class="form-control" dir="rtl">{{ old('requirements_ar', $course->requirements_ar) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.requirements_en') }}</label>
                        <textarea name="requirements_en" rows="2" class="form-control">{{ old('requirements_en', $course->requirements_en) }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-4">

        <div class="panel-card mb-3">
            <div class="panel-card-header"><h2 class="panel-card-title">{{ __('messages.publish') }}</h2></div>
            <div class="panel-card-body">
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" name="is_published" value="1" id="is_published" {{ old('is_published', $course->is_published) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_published">{{ __('messages.published') }}</label>
                </div>
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="is_featured" {{ old('is_featured', $course->is_featured) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_featured">{{ __('messages.featured') }}</label>
                </div>
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" name="is_free" value="1" id="is_free" {{ old('is_free', $course->is_free) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_free">{{ __('messages.free_course') }}</label>
                </div>
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" name="sequential_videos" value="1" id="sequential_videos" {{ old('sequential_videos', $course->sequential_videos) ? 'checked' : '' }}>
                    <label class="form-check-label" for="sequential_videos">
                        {{ __('messages.sequential_videos') }}
                        <small class="d-block text-muted" style="font-size:.75rem">{{ __('messages.sequential_videos_desc') }}</small>
                    </label>
                </div>
                <button type="submit" class="btn-primary-sm w-100 mt-3 justify-content-center">
                    <i class="bi bi-save"></i> {{ __('messages.update_course') }}
                </button>
            </div>
        </div>

        <div class="panel-card mb-3">
            <div class="panel-card-header"><h2 class="panel-card-title">{{ __('messages.details') }}</h2></div>
            <div class="panel-card-body">
                <div class="mb-3">
                    <label class="form-label">{{ __('messages.teacher') }} <span class="text-danger">*</span></label>
                    <select name="teacher_id" class="form-select" required>
                        @foreach($teachers as $t)
                            <option value="{{ $t->id }}" @selected(old('teacher_id', $course->teacher_id) == $t->id)>{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>
                {{-- Subject with full tree path --}}
                <div class="mb-3">
                    <label class="form-label">{{ __('messages.subject') }}</label>
                    <select name="subject_id" class="form-select" id="subjectSelect" onchange="syncCategoryFromSubject(this)">
                        <option value="">— {{ __('messages.none') }} —</option>
                        @foreach($subjects as $sub)
                            <option value="{{ $sub->id }}"
                                    data-cat="{{ $sub->category?->id ?? '' }}"
                                    @selected(old('subject_id', $course->subject_id) == $sub->id)>
                                {{ $sub->full_path }}
                            </option>
                        @endforeach
                    </select>
                </div>
                {{-- Root category --}}
                <div class="mb-3">
                    <label class="form-label">{{ __('messages.main_category') }}</label>
                    <select name="category_id" class="form-select" id="categorySelect">
                        <option value="">— {{ __('messages.select_option') }} —</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" @selected(old('category_id', $course->category_id) == $cat->id)>
                                {{ $cat->name_ar }} ({{ $cat->name_en }})
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">{{ __('messages.category_auto_hint') }}</small>
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('messages.difficulty') }}</label>
                    <select name="difficulty_level" class="form-select">
                        @foreach(['beginner','intermediate','advanced'] as $level)
                            <option value="{{ $level }}" @selected(old('difficulty_level', $course->difficulty_level) === $level)>{{ __('messages.'.$level) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="row g-2">
                    <div class="col-6">
                        <label class="form-label">{{ __('messages.price_usd') }}</label>
                        <input type="number" name="price" value="{{ old('price', $course->price) }}" min="0" step="0.01" class="form-control" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label">{{ __('messages.old_price_usd') }}</label>
                        <input type="number" name="old_price" value="{{ old('old_price', $course->old_price) }}" min="0" step="0.01" class="form-control">
                    </div>
                    <div class="col-12">
                        <label class="form-label">{{ __('messages.duration_hours') }}</label>
                        <input type="number" name="duration_hours" value="{{ old('duration_hours', $course->duration_hours) }}" min="0" class="form-control">
                    </div>
                </div>
            </div>
        </div>

        <div class="panel-card">
            <div class="panel-card-header"><h2 class="panel-card-title">{{ __('messages.thumbnail') }}</h2></div>
            <div class="panel-card-body">
                @if($course->thumbnail)
                    <img src="{{ asset('assets/uploads/courses/' . $course->thumbnail) }}" class="img-fluid rounded mb-2" alt="">
                @endif
                <input type="file" name="thumbnail" accept="image/*" class="form-control">
                <small class="text-muted">{{ __('messages.leave_empty_keep_image') }}</small>
            </div>
        </div>

    </div>
</div>

</form>
@endsection

@push('scripts')
<script>
function syncCategoryFromSubject(sel) {
    const catId = sel.options[sel.selectedIndex]?.dataset.cat;
    if (catId) {
        const catSel = document.getElementById('categorySelect');
        if (catSel) catSel.value = catId;
    }
}
</script>
@endpush
