<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $subjects = Subject::when($request->search, fn ($q, $s) => $q
                ->where('name_ar', 'like', "%{$s}%")
                ->orWhere('name_en', 'like', "%{$s}%")
            )
            ->orderBy('order_index')
            ->paginate(20)
            ->withQueryString();

        return view('admin.subjects.index', compact('subjects'));
    }

    public function create()
    {
        $classes = SchoolClass::where('is_active', true)->orderBy('name')->get();

        return view('admin.subjects.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name_ar'     => 'required|string|max:200',
            'name_en'     => 'required|string|max:200',
            'order_index' => 'nullable|integer|min:0',
            'is_active'   => 'boolean',
            'icon'        => 'nullable|string|max:100',
            'color_class' => 'nullable|string|max:100',
            'class_ids'   => 'nullable|array',
            'class_ids.*' => 'exists:classes,id',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $classIds = $data['class_ids'] ?? [];
        unset($data['class_ids']);

        $subject = Subject::create($data);
        $subject->classes()->sync($classIds);

        return redirect()->route('admin.subjects.index')
            ->with('success', __('messages.subject_created'));
    }

    public function edit(Subject $subject)
    {
        $classes = SchoolClass::where('is_active', true)->orderBy('name')->get();
        $selectedClassIds = $subject->classes()->pluck('classes.id')->toArray();

        return view('admin.subjects.edit', compact('subject', 'classes', 'selectedClassIds'));
    }

    public function update(Request $request, Subject $subject)
    {
        $data = $request->validate([
            'name_ar'     => 'required|string|max:200',
            'name_en'     => 'required|string|max:200',
            'order_index' => 'nullable|integer|min:0',
            'is_active'   => 'boolean',
            'icon'        => 'nullable|string|max:100',
            'color_class' => 'nullable|string|max:100',
            'class_ids'   => 'nullable|array',
            'class_ids.*' => 'exists:classes,id',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $classIds = $data['class_ids'] ?? [];
        unset($data['class_ids']);

        $subject->update($data);
        $subject->classes()->sync($classIds);

        return redirect()->route('admin.subjects.index')
            ->with('success', __('messages.subject_updated'));
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();

        return redirect()->back()
            ->with('success', __('messages.subject_deleted'));
    }
}
