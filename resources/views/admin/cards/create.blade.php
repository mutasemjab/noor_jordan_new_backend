@extends('admin.layouts.app')
@section('title', __('messages.add_card'))

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div><h1 class="page-title">{{ __('messages.add_card') }}</h1></div>
    <a href="{{ route('admin.cards.index') }}" class="btn-outline-sm"><i class="bi bi-arrow-left"></i> {{ __('messages.Back') }}</a>
</div>

@if($errors->any())
    <div class="alert alert-danger mb-3"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<div class="row g-3">
<div class="col-12 col-xl-7">
<form action="{{ route('admin.cards.store') }}" method="POST" enctype="multipart/form-data">
@csrf
<div class="panel-card">
    <div class="panel-card-header"><h2 class="panel-card-title">{{ __('messages.card_info') }}</h2></div>
    <div class="panel-card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">{{ __('messages.name_ar') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_ar" value="{{ old('name_ar') }}"
                       class="form-control @error('name_ar') is-invalid @enderror" dir="rtl" required>
                @error('name_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ __('messages.name_en') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_en" value="{{ old('name_en') }}"
                       class="form-control @error('name_en') is-invalid @enderror" required>
                @error('name_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label">{{ __('messages.pos_optional') }}</label>
                <select name="pos_id" class="form-select @error('pos_id') is-invalid @enderror">
                    <option value="">{{ __('messages.no_pos_option') }}</option>
                    @foreach($posList as $pos)
                    <option value="{{ $pos->id }}" @selected(old('pos_id') == $pos->id)>
                        {{ $pos->name_en }} — {{ $pos->city->name_en ?? '' }}
                    </option>
                    @endforeach
                </select>
                @error('pos_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ __('messages.selling_price') }} <span class="text-danger">*</span></label>
                <input type="number" name="selling_price" value="{{ old('selling_price', 0) }}"
                       step="0.01" min="0" class="form-control @error('selling_price') is-invalid @enderror" required>
                @error('selling_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ __('messages.number_of_cards') }} <span class="text-danger">*</span></label>
                <input type="number" name="number_of_cards" value="{{ old('number_of_cards', 10) }}"
                       min="1" max="1000" class="form-control @error('number_of_cards') is-invalid @enderror" required>
                <div class="form-text">سيتم توليد هذا العدد من أرقام البطاقات تلقائياً عند الحفظ.</div>
                @error('number_of_cards')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">طول الرقم (عدد الأحرف)</label>
                <input type="number" name="code_length" value="{{ old('code_length', 16) }}"
                       min="8" max="32" class="form-control">
                <div class="form-text">الافتراضي 16 حرفاً.</div>
            </div>
            <div class="col-12">
                <label class="form-label">{{ __('messages.photo_label') }}</label>
                <input type="file" name="photo" accept="image/*" class="form-control @error('photo') is-invalid @enderror">
                @error('photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- ── Activation Type ── --}}
            <div class="col-12"><hr class="my-1"></div>
            <div class="col-12">
                <label class="form-label fw-semibold">نوع تفعيل البطاقة <span class="text-danger">*</span></label>
                <div class="d-flex flex-wrap gap-3 mt-1">
                    <label class="d-flex align-items-center gap-2 p-3 rounded border cursor-pointer activation-option {{ old('activation_type','price') === 'course'  ? 'border-primary' : '' }}" style="min-width:160px">
                        <input type="radio" name="activation_type" value="course"
                               {{ old('activation_type','price') === 'course' ? 'checked' : '' }}
                               class="activation-radio" style="accent-color:var(--primary)">
                        <div>
                            <div style="font-weight:600;font-size:.9rem">دورة محددة</div>
                            <div style="font-size:.75rem;color:var(--muted)">تفعّل دورة واحدة فقط</div>
                        </div>
                    </label>
                    <label class="d-flex align-items-center gap-2 p-3 rounded border cursor-pointer activation-option {{ old('activation_type','price') === 'teacher' ? 'border-primary' : '' }}" style="min-width:160px">
                        <input type="radio" name="activation_type" value="teacher"
                               {{ old('activation_type','price') === 'teacher' ? 'checked' : '' }}
                               class="activation-radio" style="accent-color:var(--primary)">
                        <div>
                            <div style="font-weight:600;font-size:.9rem">معلم محدد</div>
                            <div style="font-size:.75rem;color:var(--muted)">تفعّل أي دورة لهذا المعلم</div>
                        </div>
                    </label>
                    <label class="d-flex align-items-center gap-2 p-3 rounded border cursor-pointer activation-option {{ old('activation_type','price') === 'price'   ? 'border-primary' : '' }}" style="min-width:160px">
                        <input type="radio" name="activation_type" value="price"
                               {{ old('activation_type','price') === 'price'   ? 'checked' : '' }}
                               class="activation-radio" style="accent-color:var(--primary)">
                        <div>
                            <div style="font-weight:600;font-size:.9rem">حسب السعر</div>
                            <div style="font-size:.75rem;color:var(--muted)">تفعّل أي دورة بنفس سعر البيع</div>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Course picker --}}
            <div class="col-12" id="pick-course" style="{{ old('activation_type','price') === 'course' ? '' : 'display:none' }}">
                <label class="form-label">اختر الدورة <span class="text-danger">*</span></label>
                <select name="linked_course_id" class="form-select @error('linked_course_id') is-invalid @enderror">
                    <option value="">— اختر دورة —</option>
                    @foreach($courses as $c)
                        <option value="{{ $c->id }}" @selected(old('linked_course_id') == $c->id)>
                            {{ $c->title_ar }} — {{ $c->teacher?->name ?? '' }}
                        </option>
                    @endforeach
                </select>
                @error('linked_course_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- Teacher picker --}}
            <div class="col-12" id="pick-teacher" style="{{ old('activation_type','price') === 'teacher' ? '' : 'display:none' }}">
                <label class="form-label">اختر المعلم <span class="text-danger">*</span></label>
                <select name="linked_teacher_id" class="form-select @error('linked_teacher_id') is-invalid @enderror">
                    <option value="">— اختر معلماً —</option>
                    @foreach($teachers as $t)
                        <option value="{{ $t->id }}" @selected(old('linked_teacher_id') == $t->id)>
                            {{ $t->name }}
                        </option>
                    @endforeach
                </select>
                @error('linked_teacher_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
                <button type="submit" class="btn-primary-sm"><i class="bi bi-save"></i> حفظ وتوليد الأرقام</button>
            </div>
        </div>
    </div>
</div>
</form>
</div>
</div>

@push('scripts')
<script>
document.querySelectorAll('.activation-radio').forEach(radio => {
    radio.addEventListener('change', function () {
        document.getElementById('pick-course').style.display  = this.value === 'course'  ? '' : 'none';
        document.getElementById('pick-teacher').style.display = this.value === 'teacher' ? '' : 'none';
        document.querySelectorAll('.activation-option').forEach(el => el.classList.remove('border-primary'));
        this.closest('.activation-option').classList.add('border-primary');
    });
});
</script>
@endpush

@endsection
