<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\FCMController;
use App\Models\Announcement;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $announcements = Announcement::with(['schoolClass', 'classes'])
            ->when($request->search, fn ($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->when($request->class_id, fn ($q, $c) => $q->where(fn ($q) => $q
                ->where('class_id', $c)
                ->orWhereHas('classes', fn ($q) => $q->where('classes.id', $c))
            ))
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        $classes = SchoolClass::where('is_active', true)->orderBy('name')->get();

        return view('admin.announcements.index', compact('announcements', 'classes'));
    }

    public function create()
    {
        $classes = SchoolClass::where('is_active', true)->orderBy('name')->get();
        return view('admin.announcements.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'body'         => 'required|string',
            'class_ids'    => 'nullable|array',
            'class_ids.*'  => 'exists:classes,id',
            'is_active'    => 'boolean',
            'published_at' => 'nullable|date',
            'image'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $classIds = $data['class_ids'] ?? [];
        unset($data['class_ids']);

        if ($request->hasFile('image')) {
            $data['image'] = uploadImage('assets/uploads/announcements', $request->file('image'));
        }

        $data['is_active']    = $request->boolean('is_active', true);
        $data['published_at'] = $data['published_at'] ?? now();
        // Kept in sync to the first target class purely so the student API's
        // existing single class_id field never has to change shape.
        $data['class_id'] = $classIds[0] ?? null;

        $announcement = Announcement::create($data);
        $announcement->classes()->sync($classIds);

        if ($announcement->is_active) {
            FCMController::sendToStudents(
                $announcement->title,
                $announcement->body,
                $this->fcmTarget($classIds),
                'announcements'
            );
        }

        return redirect()->route('admin.announcements.index')
            ->with('success', 'تم إضافة الإعلان وإرسال الإشعار بنجاح.');
    }

    public function edit(Announcement $announcement)
    {
        $classes = SchoolClass::where('is_active', true)->orderBy('name')->get();
        $selectedClassIds = $announcement->classes->pluck('id')->all()
            ?: array_filter([$announcement->class_id]);

        return view('admin.announcements.edit', compact('announcement', 'classes', 'selectedClassIds'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'body'         => 'required|string',
            'class_ids'    => 'nullable|array',
            'class_ids.*'  => 'exists:classes,id',
            'is_active'    => 'boolean',
            'published_at' => 'nullable|date',
            'image'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $classIds = $data['class_ids'] ?? [];
        unset($data['class_ids']);

        if ($request->hasFile('image')) {
            $data['image'] = uploadImage('assets/uploads/announcements', $request->file('image'));
        }

        $data['is_active'] = $request->boolean('is_active');
        $data['class_id']  = $classIds[0] ?? null;

        $announcement->update($data);
        $announcement->classes()->sync($classIds);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'تم تحديث الإعلان بنجاح.');
    }

    // Resolves the announcement's target class(es) into the concrete list of
    // student IDs FCMController::sendToStudents() already knows how to
    // handle (its array branch means "these student IDs", not class IDs).
    private function fcmTarget(array $classIds): ?array
    {
        if (empty($classIds)) {
            return null; // broadcast to everyone
        }

        return Student::where('is_active', true)
            ->whereIn('class_id', $classIds)
            ->pluck('id')
            ->all();
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();

        return back()->with('success', 'تم حذف الإعلان.');
    }
}
