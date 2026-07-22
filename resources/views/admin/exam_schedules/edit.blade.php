@extends('admin.layouts.app')
@section('title', 'تعديل جدول الامتحانات')

@section('content')
<div class="container-fluid px-4 py-3">

  {{-- Breadcrumb --}}
  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">الرئيسية</a></li>
      <li class="breadcrumb-item"><a href="{{ route('admin.exam-schedules.index') }}">جداول الامتحانات</a></li>
      <li class="breadcrumb-item active">تعديل</li>
    </ol>
  </nav>

  {{-- Page Header --}}
  <div class="d-flex align-items-center gap-3 mb-4">
    <div style="width:48px;height:48px;background:linear-gradient(135deg,#233a77,#2d4d99);border-radius:12px;display:flex;align-items:center;justify-content:center;">
      <i class="bi bi-pencil-square" style="color:#f4ae2d;font-size:22px;"></i>
    </div>
    <div>
      <h4 class="mb-0 fw-bold">تعديل جدول الامتحانات</h4>
      <small class="text-muted">{{ $examSchedule->name }}</small>
    </div>
    <a href="{{ route('admin.exam-schedules.index') }}" class="btn btn-outline-secondary btn-sm ms-auto">
      <i class="bi bi-arrow-right me-1"></i> العودة للقائمة
    </a>
  </div>

  <div class="row g-4 justify-content-center">

    {{-- Edit Form --}}
    <div class="col-lg-5">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom fw-semibold py-3">
          <i class="bi bi-pencil me-2 text-primary"></i> بيانات الجدول
        </div>
        <div class="card-body">
          <form action="{{ route('admin.exam-schedules.update', $examSchedule->id) }}"
                method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
              <label class="form-label fw-semibold">اسم الجدول <span class="text-danger">*</span></label>
              <input type="text" name="name" value="{{ old('name', $examSchedule->name) }}"
                     class="form-control @error('name') is-invalid @enderror"
                     placeholder="مثال: جدول امتحانات الفصل الأول">
              @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">الصف (اختياري)</label>
              <select name="class_id" class="form-select @error('class_id') is-invalid @enderror">
                <option value="">— عام (لكل الصفوف) —</option>
                @foreach($classes as $class)
                  <option value="{{ $class->id }}"
                          @selected(old('class_id', $examSchedule->class_id) == $class->id)>
                    {{ $class->name }}
                  </option>
                @endforeach
              </select>
              @error('class_id')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-4">
              <label class="form-label fw-semibold">صورة الجدول (اترك فارغاً للإبقاء على الحالية)</label>
              <div id="dropZone"
                   onclick="document.getElementById('imageInput').click()"
                   style="border:2.5px dashed #dee2e6;border-radius:14px;padding:32px 16px;text-align:center;cursor:pointer;transition:all .25s;background:#fafbfc;">
                <i class="bi bi-image" style="font-size:40px;color:#adb5bd;display:block;margin-bottom:10px;"></i>
                <div class="fw-semibold text-muted mb-1" style="font-size:14px;">اسحب الصورة هنا أو اضغط للاختيار</div>
                <small class="text-muted">PNG, JPG, WEBP — حد أقصى 8 MB</small>
              </div>
              <input type="file" name="image" id="imageInput" accept="image/*" class="d-none">
              @error('image')
                <div class="text-danger small mt-1"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
              @enderror
            </div>

            {{-- New image preview --}}
            <div id="previewWrap" class="mb-4 d-none">
              <label class="form-label fw-semibold text-success">
                <i class="bi bi-check-circle-fill me-1"></i>الصورة الجديدة:
              </label>
              <img id="previewImg" src="" alt="preview"
                   style="width:100%;border-radius:10px;box-shadow:0 4px 16px rgba(0,0,0,0.1);">
              <small class="text-muted d-block mt-1" id="previewFilename"></small>
            </div>

            <button type="submit" class="btn w-100 fw-bold py-2"
                    style="background:linear-gradient(135deg,#233a77,#2d4d99);color:white;border:none;border-radius:10px;">
              <i class="bi bi-save me-2"></i>حفظ التعديلات
            </button>
          </form>
        </div>
      </div>
    </div>

    {{-- Current Image --}}
    <div class="col-lg-5">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-header bg-white border-bottom fw-semibold py-3 d-flex align-items-center gap-2">
          <i class="bi bi-journal-check" style="color:#233a77;"></i>
          الصورة الحالية
          @if($examSchedule->image)
            <span class="badge bg-success ms-1">مرفوعة</span>
          @else
            <span class="badge bg-warning text-dark ms-1">لا توجد صورة</span>
          @endif
        </div>
        <div class="card-body d-flex align-items-center justify-content-center">
          @if($examSchedule->image)
            <div class="text-center w-100">
              <img src="{{ asset('assets/uploads/exam-schedules/' . $examSchedule->image) }}"
                   alt="{{ $examSchedule->name }}"
                   style="max-width:100%;border-radius:12px;box-shadow:0 8px 32px rgba(0,0,0,0.15);cursor:zoom-in;"
                   onclick="window.open(this.src,'_blank')"
                   title="اضغط لفتح الصورة بالحجم الكامل">
              <div class="mt-3 d-flex gap-2 justify-content-center">
                <a href="{{ asset('assets/uploads/exam-schedules/' . $examSchedule->image) }}"
                   target="_blank" class="btn btn-outline-primary btn-sm">
                  <i class="bi bi-arrows-fullscreen me-1"></i>عرض كامل
                </a>
                <a href="{{ asset('assets/uploads/exam-schedules/' . $examSchedule->image) }}"
                   download class="btn btn-outline-secondary btn-sm">
                  <i class="bi bi-download me-1"></i>تحميل
                </a>
              </div>
            </div>
          @else
            <div class="text-center py-5">
              <i class="bi bi-image" style="font-size:56px;color:#dee2e6;"></i>
              <h6 class="mt-3 text-muted">لا توجد صورة حالياً</h6>
            </div>
          @endif
        </div>
      </div>
    </div>

  </div>
</div>

<script>
const input   = document.getElementById('imageInput');
const dropZ   = document.getElementById('dropZone');
const prevW   = document.getElementById('previewWrap');
const prevImg = document.getElementById('previewImg');
const prevFn  = document.getElementById('previewFilename');

input.addEventListener('change', function () {
  const file = this.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = e => {
    prevImg.src = e.target.result;
    prevFn.textContent = file.name + ' (' + (file.size / 1024 / 1024).toFixed(2) + ' MB)';
    prevW.classList.remove('d-none');
    dropZ.style.borderColor = '#198754';
    dropZ.style.background  = '#f0fff4';
  };
  reader.readAsDataURL(file);
});

dropZ.addEventListener('dragover', e => {
  e.preventDefault();
  dropZ.style.borderColor = '#233a77';
  dropZ.style.background  = '#f0f4ff';
});
dropZ.addEventListener('dragleave', () => {
  dropZ.style.borderColor = '#dee2e6';
  dropZ.style.background  = '#fafbfc';
});
dropZ.addEventListener('drop', e => {
  e.preventDefault();
  const file = e.dataTransfer.files[0];
  if (!file) return;
  const dt = new DataTransfer();
  dt.items.add(file);
  input.files = dt.files;
  input.dispatchEvent(new Event('change'));
  dropZ.style.borderColor = '#dee2e6';
  dropZ.style.background  = '#fafbfc';
});
</script>
@endsection
