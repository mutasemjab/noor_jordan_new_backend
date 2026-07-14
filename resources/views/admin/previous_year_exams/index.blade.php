@extends('admin.layouts.app')
@section('title', __('messages.previous_year_exams_title'))

@section('content')

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h4>{{ __('messages.previous_year_exams_title') }}</h4>

        <a href="{{ route('admin.previous-year-exams.create') }}"
           class="btn btn-primary">
            {{ __('messages.add_new') }}
        </a>
    </div>

    <div class="card-body">

        <table class="table table-bordered">

            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('messages.year_label') }}</th>
                    <th>{{ __('messages.subject') }}</th>
                    <th>{{ __('messages.title_ar_short') }}</th>
                    <th>{{ __('messages.title_en_short') }}</th>
                    <th>{{ __('messages.pdf_file_label') }}</th>
                    <th>{{ __('messages.Status') }}</th>
                    <th width="180">{{ __('messages.Actions') }}</th>
                </tr>
            </thead>

            <tbody>

                @foreach($exams as $exam)

                    <tr>

                        <td>{{ $exam->id }}</td>

                        <td>{{ $exam->year }}</td>

                        <td>
                            {{ $exam->subject?->name_ar }}
                        </td>

                        <td>{{ $exam->title_ar }}</td>

                        <td>{{ $exam->title_en }}</td>

                        <td>
                            <a href="{{ asset('assets/uploads/previousYearExam/'.$exam->pdf_file) }}"
                               target="_blank">
                                {{ __('messages.view_pdf') }}
                            </a>
                        </td>

                        <td>
                            {!! $exam->status
                                ? '<span class="badge bg-success">'.__('messages.Active').'</span>'
                                : '<span class="badge bg-danger">'.__('messages.Inactive').'</span>' !!}
                        </td>

                        <td>

                            <a href="{{ route('admin.previous-year-exams.edit',$exam->id) }}"
                               class="btn btn-warning btn-sm">
                                {{ __('messages.Edit') }}
                            </a>

                            <form method="POST"
                                  action="{{ route('admin.previous-year-exams.destroy',$exam->id) }}"
                                  style="display:inline-block">

                                @csrf
                                @method('DELETE')

                                <button class="btn btn-danger btn-sm"
                                        onclick="return confirm('{{ __('messages.delete_confirm') }}')">
                                    {{ __('messages.Delete') }}
                                </button>

                            </form>

                        </td>

                    </tr>

                @endforeach

            </tbody>

        </table>

        {{ $exams->links() }}

    </div>
</div>

@endsection
