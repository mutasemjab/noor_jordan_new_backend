@extends('admin.layouts.app')
@section('title', 'جدول الحصص — ' . $class->name)

@section('content')
<div class="container-fluid px-4 py-3">

  {{-- Breadcrumb --}}
  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">الرئيسية</a></li>
      <li class="breadcrumb-item"><a href="{{ route('admin.classes.index') }}">الصفوف</a></li>
      <li class="breadcrumb-item"><a href="{{ route('admin.classes.show', $class->id) }}">{{ $class->name }}</a></li>
      <li class="breadcrumb-item active">جدول الحصص</li>
    </ol>
  </nav>

  {{-- Page Header --}}
  <div class="d-flex align-items-center gap-3 mb-4">
    <div style="width:48px;height:48px;background:linear-gradient(135deg,#233a77,#2d4d99);border-radius:12px;display:flex;align-items:center;justify-content:center;">
      <i class="bi bi-calendar3" style="color:#f4ae2d;font-size:22px;"></i>
    </div>
    <div>
      <h4 class="mb-0 fw-bold">جدول الحصص</h4>
      <small class="text-muted">{{ $class->name }}</small>
    </div>
    <a href="{{ route('admin.classes.show', $class->id) }}" class="btn btn-outline-secondary btn-sm ms-auto">
      <i class="bi bi-arrow-right me-1"></i> العودة للصف
    </a>
  </div>

  @if(session('success'))
    <div class="alert alert-success d-flex align-items-center gap-2">
      <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
    </div>
  @endif

  <div class="row g-4">

    {{-- Upload Form --}}
    <div class="col-lg-5">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-header bg-white border-bottom fw-semibold py-3">
          <i class="bi bi-upload me-2 text-primary"></i> رفع صورة الجدول
        </div>
        <div class="card-body">
          <form action="{{ route('admin.classes.schedule.update', $class->id) }}"
                method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
              <label class="form-label fw-semibold">صورة جدول الحصص</label>
              <div id="dropZone"
                   onclick="document.getElementById('imageInput').click()"
                   style="border:2.5px dashed #dee2e6;border-radius:14px;padding:40px 20px;text-align:center;cursor:pointer;transition:all .25s;background:#fafbfc;">
                <i class="bi bi-image" style="font-size:44px;color:#adb5bd;display:block;margin-bottom:12px;"></i>
                <div class="fw-semibold text-muted mb-1">اسحب الصورة هنا أو اضغط للاختيار</div>
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
                <i class="bi bi-check-circle-fill me-1"></i>معاينة الصورة المختارة:
              </label>
              <img id="previewImg" src="" alt="preview"
                   style="width:100%;border-radius:12px;box-shadow:0 4px 16px rgba(0,0,0,0.1);">
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

    {{-- Current Schedule Image --}}
    <div class="col-lg-7">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-header bg-white border-bottom fw-semibold py-3 d-flex align-items-center gap-2">
          <i class="bi bi-calendar-week" style="color:#233a77;"></i>
          الجدول الحالي
          @if($class->schedule_image)
            <span class="badge bg-success ms-1">مرفوع</span>
          @else
            <span class="badge bg-warning text-dark ms-1">لا يوجد جدول</span>
          @endif
        </div>
        <div class="card-body d-flex align-items-center justify-content-center">
          @if($class->schedule_image)
            <div class="text-center w-100">
              <img src="{{ asset('assets/uploads/schedules/' . $class->schedule_image) }}"
                   alt="جدول حصص {{ $class->name }}"
                   style="max-width:100%;border-radius:12px;box-shadow:0 8px 32px rgba(0,0,0,0.15);cursor:zoom-in;"
                   onclick="window.open(this.src,'_blank')"
                   title="اضغط لفتح الصورة بالحجم الكامل">
              <div class="mt-3 d-flex gap-2 justify-content-center">
                <a href="{{ asset('assets/uploads/schedules/' . $class->schedule_image) }}"
                   target="_blank" class="btn btn-outline-primary btn-sm">
                  <i class="bi bi-arrows-fullscreen me-1"></i>عرض بالحجم الكامل
                </a>
                <a href="{{ asset('assets/uploads/schedules/' . $class->schedule_image) }}"
                   download class="btn btn-outline-secondary btn-sm">
                  <i class="bi bi-download me-1"></i>تحميل
                </a>
              </div>
            </div>
          @else
            <div class="text-center py-5">
              <i class="bi bi-calendar-x" style="font-size:64px;color:#dee2e6;"></i>
              <h6 class="mt-3 text-muted">لم يتم رفع جدول بعد</h6>
              <p class="text-muted small">ارفع صورة الجدول من النموذج على اليسار</p>
            </div>
          @endif
        </div>
      </div>
    </div>

  </div>

  {{-- Structured weekly periods grid (used by the teacher app to show
       "tomorrow's periods for this class" when entering the daily plan) --}}
  <div class="row g-4 mt-1">
    <div class="col-12">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom fw-semibold py-3 d-flex align-items-center gap-2">
          <i class="bi bi-grid-3x3-gap" style="color:#233a77;"></i>
          جدول الحصص (أسبوعي ثابت)
        </div>
        <div class="card-body">
          @if($periods->isEmpty())
            <div class="text-center py-4">
              <i class="bi bi-exclamation-circle" style="font-size:40px;color:#dee2e6;"></i>
              <p class="text-muted mt-2 mb-2">لازم تضيف الحصص (أوقاتها) أولاً من إعدادات الحصص.</p>
              <a href="{{ route('admin.period-settings.index') }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-clock me-1"></i> إعدادات الحصص
              </a>
            </div>
          @else
            <form action="{{ route('admin.classes.periods.update', $class->id) }}" method="POST">
              @csrf
              <div style="overflow-x:auto">
                <table class="table table-bordered align-middle mb-0" style="min-width:900px">
                  <thead>
                    <tr class="text-center">
                      <th style="min-width:110px">الحصة</th>
                      @foreach(\App\Models\ClassSchedule::$dayNames as $dayNum => $dayName)
                        <th>{{ $dayName }}</th>
                      @endforeach
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($periods as $period)
                      <tr>
                        <td class="text-center">
                          <div class="fw-semibold">{{ $period->label }}</div>
                          <small class="text-muted">
                            {{ \Carbon\Carbon::parse($period->start_time)->format('H:i') }}
                            -
                            {{ \Carbon\Carbon::parse($period->end_time)->format('H:i') }}
                          </small>
                        </td>
                        @foreach(\App\Models\ClassSchedule::$dayNames as $dayNum => $dayName)
                          @php
                            $slot = $slots->get($dayNum . '-' . $period->period_number);
                          @endphp
                          <td style="min-width:150px">
                            <select name="periods[{{ $dayNum }}][{{ $period->period_number }}][subject_id]" class="form-select form-select-sm mb-1">
                              <option value="">— مادة —</option>
                              @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" @selected($slot && $slot->subject_id == $subject->id)>{{ $subject->name_ar }}</option>
                              @endforeach
                            </select>
                            <select name="periods[{{ $dayNum }}][{{ $period->period_number }}][teacher_id]" class="form-select form-select-sm">
                              <option value="">— معلم —</option>
                              @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" @selected($slot && $slot->teacher_id == $teacher->id)>{{ $teacher->name }}</option>
                              @endforeach
                            </select>
                          </td>
                        @endforeach
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
              <button type="submit" class="btn mt-3 fw-bold px-4 py-2"
                      style="background:linear-gradient(135deg,#233a77,#2d4d99);color:white;border:none;border-radius:10px;">
                <i class="bi bi-save me-2"></i>حفظ جدول الحصص
              </button>
            </form>
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
