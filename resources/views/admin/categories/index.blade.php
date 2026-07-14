@extends('admin.layouts.app')
@section('title', __('messages.categories_title'))

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-title">{{ __('messages.categories_title') }}</h1>
        <p class="page-sub">{{ __('messages.manage_categories_tree_desc') }}</p>
    </div>
    <a href="{{ route('admin.categories.create') }}" class="btn-primary-sm">
        <i class="bi bi-plus-circle"></i> {{ __('messages.add_main_category') }}
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3">
        {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Legend --}}
<div class="d-flex gap-3 flex-wrap mb-3 small text-muted">
    <span><i class="bi bi-diagram-3 text-primary"></i> {{ __('messages.main_category') }}</span>
    <span><i class="bi bi-folder2 text-success"></i> {{ __('messages.sub_category') }}</span>
    <span><i class="bi bi-folder2-open text-info"></i> {{ __('messages.sub_sub_category') }}</span>
    <span><i class="bi bi-journal-bookmark text-danger"></i> {{ __('messages.subject') }}</span>
    <span class="ms-auto">
        <button class="btn btn-link btn-sm p-0" onclick="expandAll()">{{ __('messages.expand_all') }}</button>
        |
        <button class="btn btn-link btn-sm p-0" onclick="collapseAll()">{{ __('messages.collapse_all') }}</button>
    </span>
</div>

<div class="panel-card">
    <div class="panel-card-body p-3" id="categoryTree">
        @forelse($roots as $root)
            @include('admin.categories._node', ['nodes' => collect([$root])])
        @empty
            <div class="text-center py-5 text-muted">
                <i class="bi bi-diagram-3 fs-1 d-block mb-2"></i>
                {{ __('messages.no_categories_yet') }}
                <br>
                <a href="{{ route('admin.categories.create') }}" class="btn-primary-sm mt-3 d-inline-block">
                    {{ __('messages.add_main_category') }}
                </a>
            </div>
        @endforelse
    </div>
</div>

@endsection

@push('scripts')
<script>
function expandAll() {
    document.querySelectorAll('#categoryTree .collapse').forEach(el => {
        bootstrap.Collapse.getOrCreateInstance(el).show();
    });
}
function collapseAll() {
    document.querySelectorAll('#categoryTree .collapse').forEach(el => {
        bootstrap.Collapse.getOrCreateInstance(el).hide();
    });
}

// Rotate chevron on collapse toggle
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(btn => {
        const target = document.querySelector(btn.dataset.bsTarget);
        if (!target) return;
        target.addEventListener('show.bs.collapse',  () => btn.querySelector('.toggle-icon').style.transform = 'rotate(90deg)');
        target.addEventListener('hide.bs.collapse',  () => btn.querySelector('.toggle-icon').style.transform = 'rotate(0deg)');
    });
});
</script>
<style>
.tree-node { transition: background .15s; }
.tree-node:hover > .d-flex { background: #f8f9fa; }
.toggle-icon { transition: transform .2s ease; display: inline-block; }
.btn-xs { padding: 2px 6px !important; font-size: .7rem !important; }
</style>
@endpush
