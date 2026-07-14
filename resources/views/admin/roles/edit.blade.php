@extends('admin.layouts.app')
@section('title', __('messages.edit_role'))

@section('content')
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a
                                    href="{{ route('admin.role.index') }}">{{ __('messages.role') }}</a></li>
                            <li class="breadcrumb-item active">{{ __('messages.create') }}</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{ __('messages.edit_role') }}</h4>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-6">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.role.update', $data->id) }}" method="post">
                            @csrf
                            {{ method_field('PATCH') }}
                             <div class="my-3">
                                <input type="text"
                                    class="form-control @if ($errors->has('name')) is-invalid @endif" id="name"
                                    placeholder=" {{ __('messages.name_field') }}" value="{{ $data->name }}" name="name">
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                                <span class="emsg text-danger"></span>
                            </div>
                            <div class="my-3">
                                @foreach($permissions as $value)

                                     <br>
                                     <input   class="ml-5" type="checkbox" name="perms[]" id="perm_{{$value->id}}" value="{{ $value->id }}" {{in_array($value->id, $role_permissions) ? 'checked':''}}>
                                    <label for="perm_{{$value->id}}"> {{ $value->name }}. </label>
                                    <br>
                                @endforeach
                            </div>
                            <div class="row" id="permissions">
                                @error('perms')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                                <span class="emsg text-danger"></span>
                            </div>





                    </div>




                    <div class="text-right">
                        <button type="submit"
                            class="btn btn-success waves-effect waves-light">{{ __('messages.update') }}</button>
                        <a type="button" href="{{ route('admin.role.index') }}"
                            class="btn btn-danger waves-effect waves-light m-l-10">{{ __('messages.Cancel') }}
                        </a>
                    </div>


                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
