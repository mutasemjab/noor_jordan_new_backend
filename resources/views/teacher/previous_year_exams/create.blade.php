@extends('teacher.layouts.app')
@section('title', __('messages.t_previous_year_exams'))

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div><h1 class="page-title">{{ __('messages.add_previous_year_exam') }}</h1></div>
    <a href="{{ route('teacher.previous-year-exams.index') }}" class="btn-outline-sm">
        <i class="bi bi-arrow-left"></i> {{ __('messages.Back') }}
    </a>
</div>

@if($errors->any())
    <div class="alert alert-danger mb-3">{{ $errors->first() }}</div>
@endif

<form action="{{ route('teacher.previous-year-exams.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row g-3">
        <div class="col-12 col-xl-8">
            <div class="panel-card">
                <div class="panel-card-header"><h2 class="panel-card-title">{{ __('messages.basic_info') }}</h2></div>
                <div class="panel-card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.title_ar_short') }} <span class="text-danger">*</span></label>
                            <input type="text" name="title_ar" class="form-control" value="{{ old('title_ar') }}" dir="rtl" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.title_en_short') }}</label>
                            <input type="text" name="title_en" class="form-control" value="{{ old('title_en') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.tag_ar') }}</label>
                            <input type="text" name="tag_ar" class="form-control" value="{{ old('tag_ar') }}" dir="rtl">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.tag_en') }}</label>
                            <input type="text" name="tag_en" class="form-control" value="{{ old('tag_en') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">{{ __('messages.subject') }}</label>
                            <select name="subject_id" class="form-control">
                                <option value="">— {{ __('messages.select_subject') }} —</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->full_path }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-4">
            <div class="panel-card mb-3">
                <div class="panel-card-header"><h2 class="panel-card-title">{{ __('messages.file_details') }}</h2></div>
                <div class="panel-card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">{{ __('messages.pdf_file_label') }} <span class="text-danger">*</span></label>
                            <input type="file" name="pdf_file" class="form-control" accept=".pdf" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">{{ __('messages.year_label') }}</label>
                            <input type="number" name="year" class="form-control" value="{{ old('year', date('Y')) }}" min="1900" max="2100">
                        </div>
                        <div class="col-6">
                            <label class="form-label">{{ __('messages.pages_label') }}</label>
                            <input type="number" name="pages" class="form-control" value="{{ old('pages', 0) }}" min="0">
                        </div>
                        <div class="col-6">
                            <label class="form-label">{{ __('messages.file_size_mb') }}</label>
                            <input type="number" name="file_size" class="form-control" value="{{ old('file_size') }}" step="0.01">
                        </div>
                        <div class="col-6">
                            <label class="form-label">{{ __('messages.sort_order_label') }}</label>
                            <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">{{ __('messages.Status') }}</label>
                            <select name="status" class="form-control">
                                <option value="1" selected>{{ __('messages.Active') }}</option>
                                <option value="0">{{ __('messages.Inactive') }}</option>
                            </select>
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
