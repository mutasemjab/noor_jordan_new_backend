@extends('admin.layouts.app')
@section('title', 'إرسال إشعار')

@section('content')

<div class="page-header">
    <h1 class="page-title">إرسال إشعار للطلاب</h1>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if($errors->any())
    <div class="alert alert-danger mb-3"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<div class="row g-3">
<div class="col-12 col-xl-7">
<form action="{{ route('admin.notifications.send') }}" method="POST">
@csrf
<div class="panel-card">
    <div class="panel-card-header"><h2 class="panel-card-title">محتوى الإشعار</h2></div>
    <div class="panel-card-body">
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label">العنوان <span class="text-danger">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" class="form-control @error('title') is-invalid @enderror" required>
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label">نص الإشعار <span class="text-danger">*</span></label>
                <textarea name="body" rows="4" class="form-control @error('body') is-invalid @enderror" required>{{ old('body') }}</textarea>
                @error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
                <label class="form-label">إرسال إلى <span class="text-danger">*</span></label>
                <div class="d-flex gap-3 flex-wrap">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="target" id="t_all" value="all"
                               @checked(old('target', 'all') === 'all') onchange="showTarget(this.value)">
                        <label class="form-check-label" for="t_all">كل الطلاب</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="target" id="t_class" value="class"
                               @checked(old('target') === 'class') onchange="showTarget(this.value)">
                        <label class="form-check-label" for="t_class">صف محدد</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="target" id="t_student" value="student"
                               @checked(old('target') === 'student') onchange="showTarget(this.value)">
                        <label class="form-check-label" for="t_student">طالب محدد</label>
                    </div>
                </div>
            </div>

            <div class="col-12" id="class_select" style="{{ old('target') === 'class' ? '' : 'display:none' }}">
                <label class="form-label">اختر الصف</label>
                <select name="class_id" class="form-select @error('class_id') is-invalid @enderror">
                    <option value="">— اختر —</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" @selected(old('class_id') == $class->id)>{{ $class->name }}</option>
                    @endforeach
                </select>
                @error('class_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12" id="student_select" style="{{ old('target') === 'student' ? '' : 'display:none' }}">
                <label class="form-label">اختر الطالب</label>
                <select name="student_id" class="form-select @error('student_id') is-invalid @enderror">
                    <option value="">— اختر —</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}" @selected(old('student_id') == $student->id)>
                            {{ $student->name }} @if($student->national_id)({{ $student->national_id }})@endif
                        </option>
                    @endforeach
                </select>
                @error('student_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
                <button type="submit" class="btn-primary-sm"><i class="bi bi-send"></i> إرسال الإشعار</button>
            </div>
        </div>
    </div>
</div>
</form>
</div>

<div class="col-12 col-xl-5">
    <div class="panel-card">
        <div class="panel-card-header"><h2 class="panel-card-title">ملاحظات</h2></div>
        <div class="panel-card-body">
            <ul class="mb-0" style="line-height:2">
                <li>الإشعار يُخزَّن في قاعدة البيانات للطلاب ويمكنهم رؤيته في التطبيق</li>
                <li>الإشعار يُرسل عبر FCM فقط للطلاب الذين فعّلوا التطبيق ومنحوا إذن الإشعارات</li>
                <li>إرسال "كل الطلاب" قد يستغرق وقتاً إذا كان العدد كبيراً</li>
            </ul>
        </div>
    </div>
</div>
</div>

@endsection

@push('scripts')
<script>
function showTarget(val) {
    document.getElementById('class_select').style.display   = val === 'class'   ? '' : 'none';
    document.getElementById('student_select').style.display = val === 'student' ? '' : 'none';
}
</script>
@endpush
