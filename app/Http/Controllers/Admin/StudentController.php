<?php

namespace App\Http\Controllers\Admin;

use App\Exports\StudentsExport;
use App\Http\Controllers\Controller;
use App\Imports\StudentsImport;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Services\GoogleMapsService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $classes = SchoolClass::orderBy('name')->get();

        $students = Student::when($request->search, function ($q, $s) {
                $q->where(function ($q) use ($s) {
                    $q->where('name', 'like', "%{$s}%")
                      ->orWhere('email', 'like', "%{$s}%")
                      ->orWhere('national_id', 'like', "%{$s}%");
                });
            })
            ->when($request->is_active !== null && $request->is_active !== '', fn ($q) =>
                $q->where('is_active', $request->boolean('is_active'))
            )
            ->when($request->class_id, fn ($q, $id) =>
                $q->where('class_id', $id)
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.students.index', compact('students', 'classes'));
    }

    public function create()
    {
        $classes = SchoolClass::where('is_active', true)->orderBy('name')->get();
        return view('admin.students.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:200',
            'national_id'  => 'nullable|string|max:50|unique:students,national_id',
            'email'        => 'nullable|email|unique:students,email',
            'phone'        => 'nullable|string|max:20',
            'password'     => 'required|string|min:8|confirmed',
            'gender'       => 'nullable|in:male,female',
            'nationality'  => 'nullable|string|max:100',
            'class_id'     => 'nullable|exists:classes,id',
            'avatar'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:1024',
            'location_url' => 'nullable|url|max:2048',
        ]);

        $this->applyLocationFromUrl($request, $data);

        if ($request->hasFile('avatar')) {
            $data['avatar'] = uploadImage('public/uploads/students', $request->file('avatar'));
        }

        Student::create($data);

        return redirect()->route('admin.students.index')
            ->with('success', 'Student created successfully.');
    }

    public function show(Student $student)
    {
        $student->load(['examAttempts.exam']);

        return view('admin.students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        $classes  = SchoolClass::where('is_active', true)->orderBy('name')->get();
        $allStudents = Student::where('id', '!=', $student->id)->orderBy('name')->get(['id', 'name', 'class_id']);
        $siblingIds  = $student->siblings()->pluck('students.id')->toArray();

        return view('admin.students.edit', compact('student', 'classes', 'allStudents', 'siblingIds'));
    }

    public function update(Request $request, Student $student)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:200',
            'national_id'  => 'nullable|string|max:50|unique:students,national_id,' . $student->id,
            'email'        => 'nullable|email|unique:students,email,' . $student->id,
            'phone'        => 'nullable|string|max:20',
            'password'     => 'nullable|string|min:8|confirmed',
            'gender'       => 'nullable|in:male,female',
            'nationality'  => 'nullable|string|max:100',
            'class_id'     => 'nullable|exists:classes,id',
            'is_active'    => 'boolean',
            'avatar'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:1024',
            'siblings'     => 'nullable|array',
            'siblings.*'   => 'exists:students,id',
            'location_url' => 'nullable|url|max:2048',
        ]);

        $this->applyLocationFromUrl($request, $data);

        $data['is_active'] = $request->boolean('is_active');

        if (empty($data['password'])) {
            unset($data['password']);
        }

        if ($request->hasFile('avatar')) {
            $data['avatar'] = uploadImage('public/uploads/students', $request->file('avatar'));
        }

        $student->update($data);

        $student->syncSiblings($request->input('siblings', []));

        return redirect()->route('admin.students.index')
            ->with('success', 'تم تحديث بيانات الطالب بنجاح.');
    }

    /**
     * Turn a pasted Google Maps link into home_lat/home_lng on $data, and
     * drop location_url itself (it isn't a real column). Leaves any existing
     * home_lat/home_lng untouched if no link was pasted this time.
     */
    private function applyLocationFromUrl(Request $request, array &$data): void
    {
        $url = $data['location_url'] ?? null;
        unset($data['location_url']);

        if (! $url) {
            return;
        }

        $coords = GoogleMapsService::extractLatLngFromUrl($url);

        if (! $coords) {
            throw ValidationException::withMessages([
                'location_url' => 'تعذر استخراج الموقع من هذا الرابط، تأكد أنه رابط صحيح من خرائط جوجل.',
            ]);
        }

        $data['home_lat'] = $coords['lat'];
        $data['home_lng'] = $coords['lng'];
    }

    public function destroy(Student $student)
    {
        $student->delete();

        return redirect()->route('admin.students.index')
            ->with('success', 'Student deleted successfully.');
    }

    public function export(Request $request)
    {
        $filters  = $request->only(['search', 'is_active']);
        $filename = 'students_' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(new StudentsExport($filters), $filename);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        $importer = new StudentsImport();
        Excel::import($importer, $request->file('file'));

        $msg = "تم استيراد {$importer->imported} طالب بنجاح.";
        if ($importer->skipped) $msg .= " تم تخطي {$importer->skipped}.";
        if ($importer->errors)  $msg .= ' أخطاء: ' . implode(' | ', $importer->errors);

        return back()->with('success', $msg);
    }
}
