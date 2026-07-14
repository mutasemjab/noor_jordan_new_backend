<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $subjects = Subject::with(['category.parent.parent'])
            ->when($request->search, fn ($q, $s) => $q
                ->where('name_ar', 'like', "%{$s}%")
                ->orWhere('name_en', 'like', "%{$s}%")
            )
            ->when($request->category_id, fn ($q, $c) => $q->where('category_id', $c))
            ->orderBy('order_index')
            ->paginate(20)
            ->withQueryString();

        $categories = $this->leafCategoriesWithPath();

        return view('admin.subjects.index', compact('subjects', 'categories'));
    }

    public function create(Request $request)
    {
        $categories   = $this->leafCategoriesWithPath();
        $parentPreset = $request->category_id ? Category::find($request->category_id) : null;

        return view('admin.subjects.create', compact('categories', 'parentPreset'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'name_ar'     => 'required|string|max:200',
            'name_en'     => 'required|string|max:200',
            'order_index' => 'nullable|integer|min:0',
            'is_active'   => 'boolean',
            'icon'        => 'nullable|string|max:100',
            'color_class' => 'nullable|string|max:100',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        Subject::create($data);

        // Redirect back to categories tree if came from there
        if ($request->input('redirect_to_tree')) {
            return redirect()->route('admin.categories.index')
                ->with('success', __('messages.subject_created'));
        }

        return redirect()->route('admin.subjects.index')
            ->with('success', __('messages.subject_created'));
    }

    public function edit(Subject $subject)
    {
        $categories = $this->leafCategoriesWithPath();

        return view('admin.subjects.edit', compact('subject', 'categories'));
    }

    public function update(Request $request, Subject $subject)
    {
        $data = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'name_ar'     => 'required|string|max:200',
            'name_en'     => 'required|string|max:200',
            'order_index' => 'nullable|integer|min:0',
            'is_active'   => 'boolean',
            'icon'        => 'nullable|string|max:100',
            'color_class' => 'nullable|string|max:100',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $subject->update($data);

        return redirect()->route('admin.subjects.index')
            ->with('success', __('messages.subject_updated'));
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();

        return redirect()->back()
            ->with('success', __('messages.subject_deleted'));
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    private function leafCategoriesWithPath(): \Illuminate\Support\Collection
    {
        return Category::where('level', 2)
            ->with(['parent.parent'])
            ->get()
            ->sortBy(fn ($c) =>
                ($c->parent?->parent?->order_index ?? 99) . '|' .
                ($c->parent?->order_index ?? 99) . '|' .
                ($c->order_index ?? 99)
            )
            ->values()
            ->map(function ($c) {
                $root   = $c->parent?->parent;
                $parent = $c->parent;
                $parts  = array_filter([
                    $root?->name_ar,
                    $parent?->name_ar,
                    $c->name_ar,
                ]);
                $c->full_path = implode(' › ', $parts);
                return $c;
            });
    }
}
