@extends('admin.layouts.app')
@section('title', __('messages.pos_title'))

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-title">{{ __('messages.pos_title') }}</h1>
        <p class="page-sub">{{ __('messages.manage_pos_desc') }}</p>
    </div>
    <a href="{{ route('admin.pos.create') }}" class="btn-primary-sm"><i class="bi bi-plus-circle"></i> {{ __('messages.add_pos') }}</a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="panel-card mb-3">
    <div class="panel-card-body">
        <form method="GET" class="row g-2">
            <div class="col-12 col-md-5">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="{{ __('messages.search_name_phone_ph') }}">
            </div>
            <div class="col-12 col-md-4">
                <select name="city_id" class="form-select form-select-sm">
                    <option value="">{{ __('messages.all_cities') }}</option>
                    @foreach($cities as $city)
                    <option value="{{ $city->id }}" @selected(request('city_id') == $city->id)>{{ $city->name_en }}</option>
                    @endforeach
                </select>
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
                    <th>{{ __('messages.name_field') }}</th>
                    <th>{{ __('messages.city_label') }}</th>
                    <th>{{ __('messages.phone_label') }}</th>
                    <th>{{ __('messages.cards') }}</th>
                    <th>{{ __('messages.map') }}</th>
                    <th>{{ __('messages.Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pos as $item)
                <tr>
                    <td style="color:var(--muted)">{{ $item->id }}</td>
                    <td>
                        <div style="font-weight:500">{{ $item->name_en }}</div>
                        <div style="font-size:.75rem;color:var(--muted)" dir="rtl">{{ $item->name_ar }}</div>
                    </td>
                    <td>{{ $item->city->name_en ?? '—' }}</td>
                    <td>{{ $item->phone }}</td>
                    <td>{{ $item->cards_count }}</td>
                    <td>
                        @if($item->google_map_link)
                            <a href="{{ $item->google_map_link }}" target="_blank" class="btn-outline-sm" style="padding:3px 8px;font-size:.75rem">
                                <i class="bi bi-geo-alt"></i> {{ __('messages.map') }}
                            </a>
                        @else
                            <span style="color:var(--muted)">—</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.pos.edit', $item->id) }}" class="btn-outline-sm" style="padding:4px 8px"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.pos.destroy', $item->id) }}" method="POST" onsubmit="return confirm('{{ __('messages.delete_pos_confirm') }}')">
                                @csrf @method('DELETE')
                                <button class="btn-outline-sm" style="padding:4px 8px;color:#dc2626;border-color:#fecaca"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-4" style="color:var(--muted)">{{ __('messages.no_pos_yet') }}</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-3">{{ $pos->withQueryString()->links() }}</div>
    </div>
</div>

@endsection
