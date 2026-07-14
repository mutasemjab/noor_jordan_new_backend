@extends('admin.layouts.app')
@section('title', __('messages.previous_year_exam_singular'))

@section('content')
    <form action="{{ route('admin.previous-year-exams.store') }}" method="POST" enctype="multipart/form-data">

        @csrf

        <div class="card">

            <div class="card-header">
                <h4>{{ __('messages.previous_year_exam_singular') }}</h4>
            </div>

            <div class="card-body">

                <div class="mb-3">
                    <label>{{ __('messages.subject') }}</label>

                    <select name="subject_id" class="form-control">
                        <option value="">— اختر المادة —</option>
                        @foreach ($subjects as $subject)
                            <option value="{{ $subject->id }}"
                                {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                {{ $subject->full_path }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label>{{ __('messages.year_label') }}</label>

                    <input type="number" name="year" class="form-control">
                </div>

                <div class="mb-3">
                    <label>{{ __('messages.title_ar_short') }}</label>

                    <input type="text" name="title_ar" class="form-control">
                </div>

                <div class="mb-3">
                    <label>{{ __('messages.title_en_short') }}</label>

                    <input type="text" name="title_en" class="form-control">
                </div>

                <div class="mb-3">
                    <label>{{ __('messages.tag_ar') }}</label>

                    <input type="text" name="tag_ar" class="form-control">
                </div>

                <div class="mb-3">
                    <label>{{ __('messages.tag_en') }}</label>

                    <input type="text" name="tag_en" class="form-control">
                </div>

                <div class="mb-3">
                    <label>{{ __('messages.pages_label') }}</label>

                    <input type="number" name="pages" class="form-control">
                </div>

                <div class="mb-3">
                    <label>{{ __('messages.file_size_mb') }}</label>

                    <input type="number" step="0.01" name="file_size" class="form-control">
                </div>

                <div class="mb-3">
                    <label>{{ __('messages.sort_order_label') }}</label>

                    <input type="number" name="sort_order" class="form-control">
                </div>

                <div class="mb-3">
                    <label>{{ __('messages.pdf_file_label') }}</label>

                    <input type="file" name="pdf_file" class="form-control">

                </div>

                <div class="mb-3">

                    <label>{{ __('messages.Status') }}</label>

                    <select name="status" class="form-control">

                        <option value="1">{{ __('messages.Active') }}</option>

                        <option value="0">
                            {{ __('messages.Inactive') }}
                        </option>

                    </select>

                </div>

            </div>

            <div class="card-footer">

                <button class="btn btn-success">
                    {{ __('messages.Save') }}
                </button>

            </div>

        </div>

    </form>
@endsection
