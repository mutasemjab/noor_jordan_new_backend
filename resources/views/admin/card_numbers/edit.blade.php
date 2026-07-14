@extends('admin.layouts.app')
@section('title', __('messages.edit_card_number'))

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div><h1 class="page-title">{{ __('messages.edit_card_number') }}</h1></div>
    <a href="{{ route('admin.card-numbers.index') }}" class="btn-outline-sm"><i class="bi bi-arrow-left"></i> {{ __('messages.Back') }}</a>
</div>

@if($errors->any())
    <div class="alert alert-danger mb-3"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<div class="row g-3">
<div class="col-12 col-xl-6">
<form action="{{ route('admin.card-numbers.update', $cardNumber->id) }}" method="POST">
@csrf @method('PUT')
<div class="panel-card">
    <div class="panel-card-header"><h2 class="panel-card-title">{{ __('messages.card_number_details') }}</h2></div>
    <div class="panel-card-body">
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label">{{ __('messages.card_label') }} <span class="text-danger">*</span></label>
                <select name="card_id" class="form-select @error('card_id') is-invalid @enderror" required>
                    <option value="">{{ __('messages.select_card_ph') }}</option>
                    @foreach($cards as $card)
                    <option value="{{ $card->id }}" @selected(old('card_id', $cardNumber->card_id) == $card->id)>{{ $card->name_en }}</option>
                    @endforeach
                </select>
                @error('card_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label">{{ __('messages.card_number_field') }} <span class="text-danger">*</span></label>
                <input type="text" name="number" value="{{ old('number', $cardNumber->number) }}"
                       class="form-control font-monospace @error('number') is-invalid @enderror" required>
                @error('number')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ __('messages.activate_label') }} <span class="text-danger">*</span></label>
                <select name="activate" class="form-select @error('activate') is-invalid @enderror" required>
                    <option value="1" @selected(old('activate', $cardNumber->activate) == 1)>{{ __('messages.Active') }}</option>
                    <option value="2" @selected(old('activate', $cardNumber->activate) == 2)>{{ __('messages.Inactive') }}</option>
                </select>
                @error('activate')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ __('messages.Status') }} <span class="text-danger">*</span></label>
                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                    <option value="2" @selected(old('status', $cardNumber->status) == 2)>{{ __('messages.not_used') }}</option>
                    <option value="1" @selected(old('status', $cardNumber->status) == 1)>{{ __('messages.used') }}</option>
                </select>
                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ __('messages.sell_label') }} <span class="text-danger">*</span></label>
                <select name="sell" class="form-select @error('sell') is-invalid @enderror" required>
                    <option value="2" @selected(old('sell', $cardNumber->sell) == 2)>{{ __('messages.not_sold') }}</option>
                    <option value="1" @selected(old('sell', $cardNumber->sell) == 1)>{{ __('messages.sold') }}</option>
                </select>
                @error('sell')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <button type="submit" class="btn-primary-sm"><i class="bi bi-save"></i> {{ __('messages.save_changes') }}</button>
            </div>
        </div>
    </div>
</div>
</form>
</div>
</div>

@endsection
