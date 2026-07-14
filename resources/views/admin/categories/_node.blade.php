{{-- Recursive tree node --}}
@foreach($nodes as $node)
@php $nodeId = 'node-' . $node->id; @endphp
<div class="tree-node ms-{{ $node->level * 3 }}" data-level="{{ $node->level }}">
    <div class="d-flex align-items-center gap-2 py-2 border-bottom">
        {{-- Toggle children button --}}
        @if($node->children->count() || $node->subjects->count())
            <button class="btn btn-link btn-sm p-0 text-secondary toggle-btn"
                    data-bs-toggle="collapse" data-bs-target="#{{ $nodeId }}"
                    title="{{ __('messages.expand') }}">
                <i class="bi bi-chevron-right toggle-icon"></i>
            </button>
        @else
            <span style="width:20px"></span>
        @endif

        {{-- Icon/level badge --}}
        @php
            $icons   = ['bi-diagram-3', 'bi-folder2', 'bi-folder2-open', 'bi-folder'];
            $classes = ['text-primary', 'text-success', 'text-info', 'text-warning'];
            $ic      = $icons[min($node->level, 3)];
            $cl      = $classes[min($node->level, 3)];
        @endphp
        <i class="bi {{ $ic }} {{ $cl }} fs-5"></i>

        {{-- Name --}}
        <span class="fw-semibold flex-grow-1">
            {{ $node->name_ar }}
            <small class="text-muted fw-normal ms-1">{{ $node->name_en }}</small>
        </span>

        {{-- Action buttons --}}
        <div class="d-flex gap-1">
            {{-- Add sub-category --}}
            <a href="{{ route('admin.categories.create', ['parent_id' => $node->id]) }}"
               class="btn btn-outline-success btn-sm" title="{{ __('messages.add_sub_category') }}">
                <i class="bi bi-folder-plus"></i>
            </a>
            {{-- Add subject --}}
            <a href="{{ route('admin.subjects.create', ['category_id' => $node->id, 'redirect_to_tree' => 1]) }}"
               class="btn btn-outline-primary btn-sm" title="{{ __('messages.add_subject') }}">
                <i class="bi bi-book-half"></i>
            </a>
            {{-- Edit --}}
            <a href="{{ route('admin.categories.edit', $node) }}"
               class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-pencil"></i>
            </a>
            {{-- Delete --}}
            <form action="{{ route('admin.categories.destroy', $node) }}" method="POST"
                  onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                @csrf @method('DELETE')
                <button class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-trash"></i>
                </button>
            </form>
        </div>
    </div>

    {{-- Collapsible children + subjects --}}
    @if($node->children->count() || $node->subjects->count())
    <div id="{{ $nodeId }}" class="collapse">
        {{-- Subjects under this node --}}
        @foreach($node->subjects as $subject)
        <div class="d-flex align-items-center gap-2 py-1 border-bottom bg-light ms-{{ ($node->level + 1) * 3 }}">
            <span style="width:20px"></span>
            <i class="bi bi-journal-bookmark text-danger fs-6"></i>
            <span class="flex-grow-1 small">
                {{ $subject->name_ar }}
                <small class="text-muted">{{ $subject->name_en }}</small>
            </span>
            <div class="d-flex gap-1">
                <a href="{{ route('admin.subjects.edit', $subject) }}"
                   class="btn btn-outline-secondary btn-sm btn-xs">
                    <i class="bi bi-pencil"></i>
                </a>
                <form action="{{ route('admin.subjects.destroy', $subject) }}" method="POST"
                      onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                    @csrf @method('DELETE')
                    <button class="btn btn-outline-danger btn-sm btn-xs">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </div>
        </div>
        @endforeach

        {{-- Recursive children --}}
        @if($node->children->count())
            @include('admin.categories._node', ['nodes' => $node->children])
        @endif
    </div>
    @endif
</div>
@endforeach
