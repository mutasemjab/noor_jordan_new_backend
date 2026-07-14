<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\FCMController;
use App\Models\Announcement;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $announcements = Announcement::with('schoolClass')
            ->when($request->search, fn ($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->when($request->class_id, fn ($q, $c) => $q->where('class_id', $c))
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
            'class_id'     => 'nullable|exists:classes,id',
            'is_active'    => 'boolean',
            'published_at' => 'nullable|date',
            'image'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = uploadImage('assets/uploads/announcements', $request->file('image'));
        }

        $data['is_active']    = $request->boolean('is_active', true);
        $data['published_at'] = $data['published_at'] ?? now();

        $announcement = Announcement::create($data);

        if ($announcement->is_active) {
            $target = $announcement->class_id ? (int) $announcement->class_id : null;
            FCMController::sendToStudents($announcement->title, $announcement->body, $target, 'announcements');
        }

        return redirect()->route('admin.announcements.index')
            ->with('success', 'تم إضافة الإعلان وإرسال الإشعار بنجاح.');
    }

    public function edit(Announcement $announcement)
    {
        $classes = SchoolClass::where('is_active', true)->orderBy('name')->get();
        return view('admin.announcements.edit', compact('announcement', 'classes'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'body'         => 'required|string',
            'class_id'     => 'nullable|exists:classes,id',
            'is_active'    => 'boolean',
            'published_at' => 'nullable|date',
            'image'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = uploadImage('assets/uploads/announcements', $request->file('image'));
        }

        $data['is_active'] = $request->boolean('is_active');

        $announcement->update($data);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'تم تحديث الإعلان بنجاح.');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();

        return back()->with('success', 'تم حذف الإعلان.');
    }
}
