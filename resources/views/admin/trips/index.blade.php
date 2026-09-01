@extends('admin.layouts.app')
@section('title', 'الجولات')

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-title">الجولات</h1>
        <p class="page-sub">تنظيم نقل الطلاب بالباص — ذهاباً وإياباً</p>
    </div>
    <a href="{{ route('admin.buses.index') }}" class="btn-outline-sm"><i class="bi bi-truck-front"></i> إدارة الباصات</a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-3">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="row g-3 mb-3">
    {{-- School location --}}
    <div class="col-12 col-xl-4">
        <div class="panel-card h-100">
            <div class="panel-card-header"><h2 class="panel-card-title">موقع المدرسة</h2></div>
            <div class="panel-card-body">
                <form action="{{ route('admin.trips.school-location') }}" method="POST">
                    @csrf @method('PUT')
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label" style="font-size:.8rem">خط العرض (lat)</label>
                            <input type="number" step="0.0000001" name="school_lat" value="{{ old('school_lat', $schoolLat) }}"
                                   class="form-control form-control-sm" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:.8rem">خط الطول (lng)</label>
                            <input type="number" step="0.0000001" name="school_lng" value="{{ old('school_lng', $schoolLng) }}"
                                   class="form-control form-control-sm" required>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn-primary-sm w-100 justify-content-center">
                                <i class="bi bi-geo-alt"></i> حفظ الموقع
                            </button>
                        </div>
                    </div>
                </form>
                <p class="mt-2 mb-0" style="font-size:.75rem;color:var(--muted)">
                    لازم يكون هاد الموقع محدد قبل توليد أي جولة.
                </p>
            </div>
        </div>
    </div>

    {{-- Riders overview --}}
    <div class="col-12 col-xl-3">
        <div class="stat-card h-100" style="padding:16px">
            <div style="font-size:.8rem;color:var(--muted)">طلاب الباص — ذهاباً للمدرسة</div>
            <div style="font-size:1.6rem;font-weight:800;color:var(--primary)">{{ $ridersCount['pickup'] }}</div>
        </div>
    </div>
    <div class="col-12 col-xl-3">
        <div class="stat-card h-100" style="padding:16px">
            <div style="font-size:.8rem;color:var(--muted)">طلاب الباص — إياباً للمنزل</div>
            <div style="font-size:1.6rem;font-weight:800;color:var(--primary)">{{ $ridersCount['dropoff'] }}</div>
        </div>
    </div>

    {{-- Generate --}}
    <div class="col-12 col-xl-2">
        <div class="panel-card h-100">
            <div class="panel-card-body d-flex align-items-center justify-content-center h-100">
                <button type="button" class="btn-primary-sm w-100 justify-content-center" data-bs-toggle="modal" data-bs-target="#generateModal">
                    <i class="bi bi-magic"></i> توليد جولات
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="panel-card mb-3">
    <div class="panel-card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">الاتجاه</label>
                <select name="type" class="form-select select2">
                    <option value="">الكل</option>
                    <option value="pickup" @selected(request('type') === 'pickup')>صباحاً (ذهاب للمدرسة)</option>
                    <option value="dropoff" @selected(request('type') === 'dropoff')>عصراً (إياب للمنزل)</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">الباص</label>
                <select name="bus_id" class="form-select select2">
                    <option value="">الكل</option>
                    @foreach($buses as $bus)
                        <option value="{{ $bus->id }}" @selected(request('bus_id') == $bus->id)>{{ $bus->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn-primary-sm w-100"><i class="bi bi-search"></i> تصفية</button>
            </div>
        </form>
    </div>
</div>

<div class="panel-card">
    <div class="panel-card-body p-0" style="overflow-x:auto">
        <table class="data-table" style="min-width:540px">
            <thead>
                <tr>
                    <th class="d-none d-md-table-cell">#</th>
                    <th>الجولة</th>
                    <th>الاتجاه</th>
                    <th class="d-none d-sm-table-cell">الباص</th>
                    <th class="d-none d-lg-table-cell">المرافق</th>
                    <th class="d-none d-sm-table-cell">عدد الطلاب</th>
                    <th class="d-none d-lg-table-cell">المسافة/المدة</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($trips as $trip)
                <tr>
                    <td class="d-none d-md-table-cell" style="color:var(--muted)">{{ $trip->id }}</td>
                    <td style="font-weight:600">
                        {{ $trip->name }}
                        <div class="d-sm-none" style="font-size:.72rem;color:var(--muted)">{{ $trip->bus->name }}</div>
                    </td>
                    <td>
                        <span class="pill {{ $trip->type === 'pickup' ? 'pill-info' : 'pill-warning' }}">
                            {{ $trip->type === 'pickup' ? 'صباحاً' : 'عصراً' }}
                        </span>
                    </td>
                    <td class="d-none d-sm-table-cell">{{ $trip->bus->name }} <span style="color:var(--muted);font-size:.78rem">(جولة {{ $trip->sequence_number }})</span></td>
                    <td class="d-none d-lg-table-cell">
                        @if($trip->companionTeacher)
                            <span style="font-size:.88rem">{{ $trip->companionTeacher->name }}</span>
                        @else
                            <span style="color:var(--muted);font-size:.82rem">— غير معيّن —</span>
                        @endif
                    </td>
                    <td class="d-none d-sm-table-cell"><span class="pill pill-neutral">{{ $trip->students_count }} طالب</span></td>
                    <td class="d-none d-lg-table-cell" style="font-size:.82rem">
                        @if($trip->total_distance_km)
                            {{ $trip->total_distance_km }} كم — {{ $trip->total_duration_minutes }} د
                        @else
                            —
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            @if($trip->google_maps_url)
                            <a href="{{ $trip->google_maps_url }}" target="_blank" class="btn-outline-sm" style="padding:4px 8px">
                                <i class="bi bi-map"></i>
                            </a>
                            @endif
                            <a href="{{ route('admin.trips.edit', $trip->id) }}" class="btn-outline-sm" style="padding:4px 8px">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.trips.destroy', $trip->id) }}" method="POST"
                                  onsubmit="return confirm('حذف الجولة {{ $trip->name }}؟')">
                                @csrf @method('DELETE')
                                <button class="btn-outline-sm" style="padding:4px 8px;color:#dc2626;border-color:#fecaca">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" class="text-center py-4" style="color:var(--muted)">لا توجد جولات بعد. استخدم "توليد جولات" بالأعلى.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Generate Modal --}}
<div class="modal fade" id="generateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.trips.generate') }}" method="POST" onsubmit="return onGenerateSubmit(this)">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">توليد جولات جديدة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">الاتجاه <span class="text-danger">*</span></label>
                        <select name="type" class="form-select" required>
                            <option value="pickup">صباحاً (ذهاب للمدرسة)</option>
                            <option value="dropoff">عصراً (إياب للمنزل)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الباصات المستخدمة <span class="text-danger">*</span></label>
                        <select name="bus_ids[]" class="form-select" multiple required size="{{ max(count($buses), 4) }}">
                            @foreach($buses as $bus)
                                <option value="{{ $bus->id }}">{{ $bus->name }} (سعة {{ $bus->capacity }})</option>
                            @endforeach
                        </select>
                        <small class="text-muted" style="font-size:.75rem">
                            اضغط باص واحد لاختياره لحاله، أو اضغط مع الاستمرار على Ctrl (أو Cmd على ماك) لاختيار أكثر
                            من باص. سيتم توزيع الطلاب على الباصات المختارة حسب موقعهم الجغرافي وسعة كل باص، وقد
                            يتكرر استخدام نفس الباص لأكثر من جولة إذا زاد عدد الطلاب عن السعة الكلية.
                        </small>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="replace_existing" value="1" id="replace_existing">
                        <label class="form-check-label" for="replace_existing" style="font-size:.85rem">
                            استبدال الجولات الحالية لهذا الاتجاه (إن وجدت)
                        </label>
                    </div>
                    <div class="alert alert-warning mt-3 mb-0" style="font-size:.78rem;padding:.6rem .8rem">
                        <i class="bi bi-exclamation-triangle"></i>
                        كل عملية توليد تستدعي خرائط جوجل مرة واحدة تقريباً لكل جولة ناتجة (وليس لكل طالب) — تكلفة
                        بسيطة جداً، بس تجنّب الضغط على "توليد" أكتر من مرة بدون داعي. إذا كانت الجولات موجودة أصلاً
                        ولم تُفعّل "استبدال"، ما رح يصير أي شي وما رح يتم استدعاء جوجل نهائياً.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-outline-sm" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn-primary-sm" id="generateSubmitBtn"><i class="bi bi-magic"></i> توليد</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function onGenerateSubmit(form) {
    var replacing = form.querySelector('#replace_existing').checked;

    if (replacing && !confirm('سيتم حذف كل الجولات الحالية بنفس الاتجاه، وأي تعديل يدوي عليها (مرافق، ترتيب، طلاب مُزالين). متابعة؟')) {
        return false;
    }

    // Prevent double-submit (double-click, slow network) from firing a second
    // Google Directions batch on top of the first.
    var btn = document.getElementById('generateSubmitBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> جارٍ التوليد...';

    return true;
}
</script>
@endpush

@endsection
