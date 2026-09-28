@extends('admin.layouts.app')
@section('title', __('messages.external_links'))

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div><h1 class="page-title">{{ __('messages.add_external_link') }}</h1></div>
    <a href="{{ route('admin.external-links.index') }}" class="btn-outline-sm">
        <i class="bi bi-arrow-left"></i> {{ __('messages.Back') }}
    </a>
</div>

@if($errors->any())
    <div class="alert alert-danger mb-3">{{ $errors->first() }}</div>
@endif

<form action="{{ route('admin.external-links.store') }}" method="POST">
    @csrf
    <div class="row g-3">
        <div class="col-12 col-xl-8">
            <div class="panel-card">
                <div class="panel-card-header"><h2 class="panel-card-title">{{ __('messages.link_details') }}</h2></div>
                <div class="panel-card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">{{ __('messages.teacher') }} <span class="text-danger">*</span></label>
                            <select name="teacher_id" id="teacher_id" class="form-control select2" required>
                                <option value="">— {{ __('messages.select_teacher') }} —</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">{{ __('messages.class_label') }} <span class="text-danger">*</span></label>
                            <select name="class_id" id="class_id" class="form-control select2" required>
                                <option value="">— {{ __('messages.select_class') }} —</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">{{ __('messages.subject') }} <span class="text-danger">*</span></label>
                            <select name="subject_id" id="subject_id" class="form-control select2" required>
                                <option value="">— {{ __('messages.select_subject') }} —</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>{{ $subject->name_ar }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted" style="font-size:.75rem">{{ __('messages.subject_filtered_hint') }}</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label">{{ __('messages.link_url') }} <span class="text-danger">*</span></label>
                            <input type="url" name="url" class="form-control" value="{{ old('url') }}" placeholder="https://" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-4">
            <div class="panel-card mb-3">
                <div class="panel-card-header"><h2 class="panel-card-title">{{ __('messages.Status') }}</h2></div>
                <div class="panel-card-body">
                    <select name="status" class="form-control select2">
                        <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>{{ __('messages.Active') }}</option>
                        <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>{{ __('messages.Inactive') }}</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn-primary-sm w-100 justify-content-center" style="padding:12px">
                <i class="bi bi-save"></i> {{ __('messages.Save') }}
            </button>
        </div>
    </div>
</form>

@include('admin.educational_notes._subject_filter_script')

@endsection
