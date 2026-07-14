@extends('admin.layouts.app')
@section('title', __('messages.educational_notes'))

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div><h1 class="page-title">{{ __('messages.edit_educational_note') }}</h1></div>
    <a href="{{ route('admin.educational-notes.index') }}" class="btn-outline-sm">
        <i class="bi bi-arrow-left"></i> {{ __('messages.Back') }}
    </a>
</div>

@if($errors->any())
    <div class="alert alert-danger mb-3">{{ $errors->first() }}</div>
@endif

<form action="{{ route('admin.educational-notes.update', $educationalNote->id) }}" method="POST" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div class="row g-3">
        <div class="col-12 col-xl-8">
            <div class="panel-card">
                <div class="panel-card-header"><h2 class="panel-card-title">{{ __('messages.note_details') }}</h2></div>
                <div class="panel-card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">{{ __('messages.note_type') }} <span class="text-danger">*</span></label>
                            <div class="d-flex gap-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" id="type-lesson" value="lesson"
                                           {{ old('type', $educationalNote->type) === 'lesson' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="type-lesson"><i class="bi bi-book"></i> {{ __('messages.note_type_lesson') }}</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" id="type-homework" value="homework"
                                           {{ old('type', $educationalNote->type) === 'homework' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="type-homework"><i class="bi bi-pencil-square"></i> {{ __('messages.note_type_homework') }}</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">{{ __('messages.title') }} <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" value="{{ old('title', $educationalNote->title) }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">{{ __('messages.descriptions') }}</label>
                            <textarea name="description" class="form-control" rows="4">{{ old('description', $educationalNote->description) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-4">
            <div class="panel-card mb-3">
                <div class="panel-card-header"><h2 class="panel-card-title">{{ __('messages.additional_info') }}</h2></div>
                <div class="panel-card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">{{ __('messages.teacher') }}</label>
                            <select name="teacher_id" class="form-control">
                                <option value="">— {{ __('messages.select_teacher') }} —</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" {{ old('teacher_id', $educationalNote->teacher_id) == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">{{ __('messages.class_label') }}</label>
                            <select name="class_id" class="form-control">
                                <option value="">— {{ __('messages.select_class') }} —</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}"
                                        {{ old('class_id', $educationalNote->class_id) == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">{{ __('messages.date_label') }} <span class="text-danger">*</span></label>
                            <input type="date" name="date" class="form-control" value="{{ old('date', $educationalNote->date?->format('Y-m-d')) }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">{{ __('messages.attachment_label') }}</label>
                            <input type="file" name="attachment" class="form-control">
                            @if($educationalNote->attachment)
                                <div class="mt-1" style="font-size:.8rem">
                                    {{ __('messages.current_file') }}:
                                    <a href="{{ asset('assets/uploads/educational_notes/'.$educationalNote->attachment) }}" target="_blank">
                                        <i class="bi bi-paperclip"></i> {{ $educationalNote->attachment }}
                                    </a>
                                </div>
                            @endif
                            <small class="text-muted" style="font-size:.75rem">{{ __('messages.leave_empty_keep_file') }}</small>
                        </div>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn-primary-sm w-100 justify-content-center" style="padding:12px">
                <i class="bi bi-save"></i> {{ __('messages.Save') }}
            </button>
        </div>
    </div>
</form>

@endsection
