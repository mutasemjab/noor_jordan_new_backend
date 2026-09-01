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

    <div class="card-body" style="overflow-x:auto">

        <table class="table table-bordered" style="min-width:600px">

            <thead>
                <tr>
                    <th class="d-none d-md-table-cell">#</th>
                    <th class="d-none d-sm-table-cell">{{ __('messages.year_label') }}</th>
                    <th class="d-none d-sm-table-cell">{{ __('messages.subject') }}</th>
                    <th class="d-none d-md-table-cell">{{ __('messages.class_label') }}</th>
                    <th>{{ __('messages.title_ar_short') }}</th>
                    <th class="d-none d-lg-table-cell">{{ __('messages.title_en_short') }}</th>
                    <th class="d-none d-md-table-cell">{{ __('messages.pdf_file_label') }}</th>
                    <th>{{ __('messages.Status') }}</th>
                    <th width="180">{{ __('messages.Actions') }}</th>
                </tr>
            </thead>

            <tbody>

                @foreach($exams as $exam)

                    <tr>

                        <td class="d-none d-md-table-cell">{{ $exam->id }}</td>

                        <td class="d-none d-sm-table-cell">{{ $exam->year }}</td>

                        <td class="d-none d-sm-table-cell">
                            {{ $exam->subject?->name_ar }}
                        </td>

                        <td class="d-none d-md-table-cell">{{ $exam->schoolClass?->name ?? '—' }}</td>

                        <td>
                            {{ $exam->title_ar }}
                            <div class="d-sm-none" style="font-size:.72rem;color:#888">{{ $exam->subject?->name_ar }} — {{ $exam->year }}</div>
                        </td>

                        <td class="d-none d-lg-table-cell">{{ $exam->title_en }}</td>

                        <td class="d-none d-md-table-cell">
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
