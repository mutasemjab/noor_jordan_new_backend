@extends('admin.layouts.app')
@section('title', 'فيديوهات الصف — ' . $class->name)

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-title">فيديوهات يوتيوب</h1>
        <p class="page-sub">{{ $class->name }}</p>
    </div>
    <a href="{{ route('admin.classes.show', $class->id) }}" class="btn-outline-sm">
        <i class="bi bi-arrow-left"></i> رجوع
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

{{-- Add video form --}}
<div class="panel-card mb-3">
    <div class="panel-card-header"><h2 class="panel-card-title">إضافة فيديو جديد</h2></div>
    <div class="panel-card-body">
        <form action="{{ route('admin.classes.videos.store', $class->id) }}" method="POST">
            @csrf
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">المادة <span class="text-danger">*</span></label>
                    <select name="subject_id" class="form-select select2" required>
                        <option value="">— اختر المادة —</option>
                        @foreach($class->classSubjects as $cs)
                        <option value="{{ $cs->subject_id }}">{{ $cs->subject->name_ar }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">عنوان الفيديو <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" placeholder="مثال: درس الفعل المضارع" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">رابط يوتيوب <span class="text-danger">*</span></label>
                    <input type="url" name="youtube_url" class="form-control"
                           placeholder="https://www.youtube.com/watch?v=..." required>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn-primary-sm w-100 justify-content-center">
                        <i class="bi bi-plus-circle"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Videos by subject --}}
@foreach($class->classSubjects as $cs)
@php $vids = $videosBySubject[$cs->subject_id] ?? collect(); @endphp
<div class="panel-card mb-3">
    <div class="panel-card-header d-flex align-items-center justify-content-between">
        <h2 class="panel-card-title">{{ $cs->subject->name_ar }}</h2>
        <span class="pill pill-neutral">{{ $vids->count() }} فيديو</span>
    </div>
    @if($vids->isEmpty())
    <div class="panel-card-body text-center py-3" style="color:var(--muted);font-size:.85rem">لا توجد فيديوهات لهذه المادة.</div>
    @else
    <div class="panel-card-body">
        <div class="row g-3">
            @foreach($vids as $video)
            <div class="col-sm-6 col-lg-4">
                <div style="border:1px solid #e5e7eb;border-radius:10px;overflow:hidden">
                    <a href="{{ $video->youtube_url }}" target="_blank">
                        <img src="{{ $video->thumbnail }}" alt="{{ $video->title }}"
                             style="width:100%;height:130px;object-fit:cover;display:block">
                    </a>
                    <div style="padding:10px 12px">
                        <div style="font-size:.88rem;font-weight:600;margin-bottom:6px">{{ $video->title }}</div>
                        <div class="d-flex gap-2">
                            <a href="{{ $video->youtube_url }}" target="_blank"
                               class="btn-outline-sm" style="padding:3px 8px;font-size:.75rem">
                                <i class="bi bi-youtube" style="color:#dc2626"></i> يوتيوب
                            </a>
                            <form action="{{ route('admin.classes.videos.destroy', [$class->id, $video->id]) }}"
                                  method="POST" onsubmit="return confirm('حذف هذا الفيديو؟')">
                                @csrf @method('DELETE')
                                <button class="btn-outline-sm" style="padding:3px 8px;font-size:.75rem;color:#dc2626;border-color:#fecaca">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endforeach

@endsection
