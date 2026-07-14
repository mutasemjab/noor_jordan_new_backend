@extends('admin.layouts.app')
@section('title', __('messages.cities_title'))

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-title">{{ __('messages.cities_title') }}</h1>
        <p class="page-sub">{{ __('messages.manage_cities_desc') }}</p>
    </div>
    <a href="{{ route('admin.cities.create') }}" class="btn-primary-sm"><i class="bi bi-plus-circle"></i> {{ __('messages.add_city') }}</a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="panel-card mb-3">
    <div class="panel-card-body">
        <form method="GET" class="row g-2">
            <div class="col-12 col-md-9">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="{{ __('messages.search_cities_ph') }}">
            </div>
            <div class="col-12 col-md-3">
                <button type="submit" class="btn-primary-sm w-100"><i class="bi bi-search"></i> {{ __('messages.Search') }}</button>
            </div>
        </form>
    </div>
</div>

<div class="panel-card">
    <div class="panel-card-body p-0">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('messages.city_en') }}</th>
                    <th>{{ __('messages.city_ar') }}</th>
                    <th>{{ __('messages.pos_count') }}</th>
                    <th>{{ __('messages.Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cities as $city)
                <tr>
                    <td style="color:var(--muted)">{{ $city->id }}</td>
                    <td style="font-weight:500">{{ $city->name_en }}</td>
                    <td dir="rtl">{{ $city->name_ar }}</td>
                    <td>{{ $city->pos_count }}</td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.cities.edit', $city->id) }}" class="btn-outline-sm" style="padding:4px 8px"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.cities.destroy', $city->id) }}" method="POST" onsubmit="return confirm('{{ __('messages.delete_city_confirm') }}')">
                                @csrf @method('DELETE')
                                <button class="btn-outline-sm" style="padding:4px 8px;color:#dc2626;border-color:#fecaca"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-4" style="color:var(--muted)">{{ __('messages.no_cities_yet') }}</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-3">{{ $cities->withQueryString()->links() }}</div>
    </div>
</div>

@endsection
