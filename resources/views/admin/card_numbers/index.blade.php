@extends('admin.layouts.app')
@section('title', __('messages.card_numbers_title'))

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-title">{{ __('messages.card_numbers_title') }}</h1>
        <p class="page-sub">{{ __('messages.manage_card_numbers_desc') }}</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn-outline-sm" type="button" data-bs-toggle="collapse" data-bs-target="#bulk-gen-panel">
            <i class="bi bi-lightning-charge"></i> {{ __('messages.bulk_generate') }}
        </button>
        <a href="{{ route('admin.card-numbers.print') }}?{{ http_build_query(request()->all()) }}" target="_blank" class="btn-outline-sm">
            <i class="bi bi-printer"></i> طباعة
        </a>
        <a href="{{ route('admin.card-numbers.create') }}" class="btn-primary-sm"><i class="bi bi-plus-circle"></i> {{ __('messages.add_number') }}</a>
    </div>
</div>

{{-- Bulk Generate Panel --}}
<div class="collapse mb-3" id="bulk-gen-panel">
    <div class="panel-card">
        <div class="panel-card-header">
            <h2 class="panel-card-title"><i class="bi bi-lightning-charge" style="color:#f59e0b"></i> {{ __('messages.bulk_generate') }}</h2>
            <span style="font-size:.8rem;color:var(--muted)">{{ __('messages.bulk_generate_desc') }}</span>
        </div>
        <div class="panel-card-body">
            <form action="{{ route('admin.card-numbers.bulk') }}" method="POST" class="row g-3 align-items-end">
                @csrf
                <div class="col-12 col-md-3">
                    <label class="form-label">{{ __('messages.card_label') }} <span class="text-danger">*</span></label>
                    <select name="card_id" class="form-select form-select-sm" required>
                        <option value="">{{ __('messages.select_card_ph') }}</option>
                        @foreach($cards as $c)
                        <option value="{{ $c->id }}" @selected(request('card_id') == $c->id)>{{ $c->name_en }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label">{{ __('messages.count_label') }} <span class="text-danger">*</span></label>
                    <input type="number" name="count" value="10" min="1" max="500" class="form-control form-control-sm" required>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label">{{ __('messages.length_label') }}</label>
                    <input type="number" name="length" value="16" min="8" max="32" class="form-control form-control-sm">
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label">{{ __('messages.prefix_label') }}</label>
                    <input type="text" name="prefix" maxlength="20" class="form-control form-control-sm" placeholder="BAHETH-">
                </div>
                <div class="col-12 col-md-2">
                    <button type="submit" class="btn-primary-sm w-100">
                        <i class="bi bi-lightning-charge"></i> {{ __('messages.generate_btn') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="panel-card mb-3">
    <div class="panel-card-body">
        <form method="GET" class="row g-2">
            <div class="col-12 col-md-3">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="{{ __('messages.search_number_ph') }}">
            </div>
            <div class="col-12 col-md-2">
                <select name="card_id" class="form-select form-select-sm">
                    <option value="">{{ __('messages.all_cards') }}</option>
                    @foreach($cards as $c)
                    <option value="{{ $c->id }}" @selected(request('card_id') == $c->id)>{{ $c->name_en }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-2">
                <select name="activate" class="form-select form-select-sm">
                    <option value="">{{ __('messages.activate_all') }}</option>
                    <option value="1" @selected(request('activate') === '1')>{{ __('messages.Active') }}</option>
                    <option value="2" @selected(request('activate') === '2')>{{ __('messages.Inactive') }}</option>
                </select>
            </div>
            <div class="col-12 col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">{{ __('messages.status_all') }}</option>
                    <option value="1" @selected(request('status') === '1')>{{ __('messages.used') }}</option>
                    <option value="2" @selected(request('status') === '2')>{{ __('messages.not_used') }}</option>
                </select>
            </div>
            <div class="col-12 col-md-1">
                <select name="sell" class="form-select form-select-sm">
                    <option value="">{{ __('messages.sell_all') }}</option>
                    <option value="1" @selected(request('sell') === '1')>{{ __('messages.sold') }}</option>
                    <option value="2" @selected(request('sell') === '2')>{{ __('messages.unsold') }}</option>
                </select>
            </div>
            <div class="col-12 col-md-2">
                <button type="submit" class="btn-primary-sm w-100"><i class="bi bi-search"></i> {{ __('messages.filter') }}</button>
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
                    <th>{{ __('messages.number_label') }}</th>
                    <th>{{ __('messages.card_number_field') }}</th>
                    <th>{{ __('messages.activate_label') }}</th>
                    <th>{{ __('messages.Status') }}</th>
                    <th>{{ __('messages.sell_label') }}</th>
                    <th>الطالب المفعِّل</th>
                    <th>{{ __('messages.Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cardNumbers as $cn)
                <tr>
                    <td style="color:var(--muted)">{{ $cn->id }}</td>
                    <td style="font-family:monospace;font-size:.88rem">{{ $cn->number }}</td>
                    <td>{{ $cn->card->name_en ?? '—' }}</td>
                    <td>
                        <span class="pill {{ $cn->activate === 1 ? 'pill-success' : 'pill-neutral' }}">
                            {{ $cn->activate === 1 ? __('messages.Active') : __('messages.Inactive') }}
                        </span>
                    </td>
                    <td>
                        <span class="pill {{ $cn->status === 1 ? 'pill-neutral' : 'pill-success' }}">
                            {{ $cn->status === 1 ? __('messages.used') : __('messages.not_used') }}
                        </span>
                    </td>
                    <td>
                        <span class="pill {{ $cn->sell === 1 ? 'pill-neutral' : 'pill-success' }}">
                            {{ $cn->sell === 1 ? __('messages.sold') : __('messages.unsold') }}
                        </span>
                    </td>
                    <td>
                        @if($cn->assignedUser)
                            <div style="font-weight:500;font-size:.85rem">{{ $cn->assignedUser->name }}</div>
                            <div style="font-size:.75rem;color:var(--muted)">{{ $cn->assignedUser->national_id }}</div>
                        @else
                            <span style="color:var(--muted)">—</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.card-numbers.edit', $cn->id) }}" class="btn-outline-sm" style="padding:4px 8px"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.card-numbers.destroy', $cn->id) }}" method="POST" onsubmit="return confirm('{{ __('messages.delete_number_confirm') }}')">
                                @csrf @method('DELETE')
                                <button class="btn-outline-sm" style="padding:4px 8px;color:#dc2626;border-color:#fecaca"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-4" style="color:var(--muted)">{{ __('messages.no_card_numbers_yet') }}</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-3">{{ $cardNumbers->withQueryString()->links() }}</div>
    </div>
</div>

@endsection
