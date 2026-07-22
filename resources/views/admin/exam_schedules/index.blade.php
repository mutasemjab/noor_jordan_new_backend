@extends('admin.layouts.app')
@section('title', 'جداول الامتحانات')

@section('content')
<div class="container-fluid px-4 py-3">

  {{-- Page Header --}}
  <div class="d-flex align-items-center gap-3 mb-4 flex-wrap">
    <div style="width:48px;height:48px;background:linear-gradient(135deg,#233a77,#2d4d99);border-radius:12px;display:flex;align-items:center;justify-content:center;">
      <i class="bi bi-journal-check" style="color:#f4ae2d;font-size:22px;"></i>
    </div>
    <div>
      <h4 class="mb-0 fw-bold">جداول الامتحانات</h4>
      <small class="text-muted">
        @if($selectedClass)
          عرض جداول: <strong>{{ $selectedClass->name }}</strong>
        @else
          إدارة صور جداول الامتحانات
        @endif
      </small>
    </div>
    <div class="ms-auto d-flex gap-2">
      @if($selectedClass)
        <a href="{{ route('admin.classes.show', $selectedClass->id) }}" class="btn btn-outline-secondary btn-sm">
          <i class="bi bi-arrow-right me-1"></i> العودة للصف
        </a>
        <a href="{{ route('admin.exam-schedules.index') }}" class="btn btn-outline-secondary btn-sm">
          <i class="bi bi-x-circle me-1"></i> إلغاء الفلتر
        </a>
      @endif
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success d-flex align-items-center gap-2 mb-3">
      <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
    </div>
  @endif

  <div class="row g-4">

    {{-- Add Form --}}
    <div class="col-lg-4">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom fw-semibold py-3">
          <i class="bi bi-plus-circle me-2 text-primary"></i> إضافة جدول امتحانات
        </div>
        <div class="card-body">
          <form action="{{ route('admin.exam-schedules.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
              <label class="form-label fw-semibold">اسم الجدول <span class="text-danger">*</span></label>
              <input type="text" name="name" value="{{ old('name') }}"
                     class="form-control @error('name') is-invalid @enderror"
                     placeholder="مثال: جدول امتحانات الفصل الأول">
              @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">الصف (اختياري)</label>
              <select name="class_id" class="form-select select2 @error('class_id') is-invalid @enderror">
                <option value="">— عام (لكل الصفوف) —</option>
                @foreach($classes as $class)
                  <option value="{{ $class->id }}" @selected(old('class_id', $selectedClass?->id) == $class->id)>
                    {{ $class->name }}
                  </option>
                @endforeach
              </select>
              @error('class_id')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-4">
              <label class="form-label fw-semibold">صورة الجدول <span class="text-danger">*</span></label>
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

            {{-- Preview --}}
            <div id="previewWrap" class="mb-4 d-none">
              <label class="form-label fw-semibold text-success">
                <i class="bi bi-check-circle-fill me-1"></i>معاينة:
              </label>
              <img id="previewImg" src="" alt="preview"
                   style="width:100%;border-radius:10px;box-shadow:0 4px 16px rgba(0,0,0,0.1);">
              <small class="text-muted d-block mt-1" id="previewFilename"></small>
            </div>

            <button type="submit" class="btn w-100 fw-bold py-2"
                    style="background:linear-gradient(135deg,#233a77,#2d4d99);color:white;border:none;border-radius:10px;">
              <i class="bi bi-cloud-upload me-2"></i>حفظ الجدول
            </button>
          </form>
        </div>
      </div>
    </div>

    {{-- List --}}
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom fw-semibold py-3 d-flex align-items-center gap-2">
          <i class="bi bi-list-ul" style="color:#233a77;"></i>
          قائمة الجداول
          <span class="badge ms-auto" style="background:#233a77;">{{ $examSchedules->total() }}</span>
        </div>
        <div class="card-body p-0">
          @if($examSchedules->isEmpty())
            <div class="text-center py-5">
              <i class="bi bi-journal-x" style="font-size:56px;color:#dee2e6;"></i>
              <h6 class="mt-3 text-muted">لا توجد جداول امتحانات بعد</h6>
              <p class="text-muted small">أضف أول جدول من النموذج على اليسار</p>
            </div>
          @else
            <div class="row g-3 p-3">
              @foreach($examSchedules as $es)
                <div class="col-sm-6">
                  <div class="card border shadow-sm h-100" style="border-radius:12px;overflow:hidden;">
                    @if($es->image)
                      <div style="height:160px;overflow:hidden;background:#f8f9fa;cursor:zoom-in;"
                           onclick="window.open('{{ asset('assets/uploads/exam-schedules/' . $es->image) }}','_blank')">
                        <img src="{{ asset('assets/uploads/exam-schedules/' . $es->image) }}"
                             alt="{{ $es->name }}"
                             style="width:100%;height:100%;object-fit:cover;transition:transform .3s;"
                             onmouseover="this.style.transform='scale(1.05)'"
                             onmouseout="this.style.transform='scale(1)'">
                      </div>
                    @else
                      <div style="height:160px;background:#f8f9fa;display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-image" style="font-size:40px;color:#dee2e6;"></i>
                      </div>
                    @endif
                    <div class="card-body p-3">
                      <div class="fw-semibold mb-1" style="font-size:15px;">{{ $es->name }}</div>
                      <div class="mb-2">
                        @if($es->schoolClass)
                          <span class="badge" style="background:#e8edf5;color:#233a77;font-size:12px;">
                            <i class="bi bi-mortarboard me-1"></i>{{ $es->schoolClass->name }}
                          </span>
                        @else
                          <span class="badge bg-secondary" style="font-size:12px;">عام</span>
                        @endif
                      </div>
                      <small class="text-muted">{{ $es->created_at->format('Y/m/d') }}</small>
                    </div>
                    <div class="card-footer bg-white border-top p-2 d-flex gap-2">
                      @if($es->image)
                        <a href="{{ asset('assets/uploads/exam-schedules/' . $es->image) }}"
                           target="_blank" class="btn btn-outline-primary btn-sm flex-fill">
                          <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ asset('assets/uploads/exam-schedules/' . $es->image) }}"
                           download class="btn btn-outline-secondary btn-sm flex-fill">
                          <i class="bi bi-download"></i>
                        </a>
                      @endif
                      <a href="{{ route('admin.exam-schedules.edit', $es->id) }}"
                         class="btn btn-outline-warning btn-sm flex-fill">
                        <i class="bi bi-pencil"></i>
                      </a>
                      <form action="{{ route('admin.exam-schedules.destroy', $es->id) }}" method="POST"
                            class="flex-fill" onsubmit="return confirm('هل أنت متأكد من حذف هذا الجدول؟')">
                        @csrf @method('DELETE')
                        <button class="btn btn-outline-danger btn-sm w-100">
                          <i class="bi bi-trash"></i>
                        </button>
                      </form>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>

            @if($examSchedules->hasPages())
              <div class="p-3 border-top">{{ $examSchedules->links() }}</div>
            @endif
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
