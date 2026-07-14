<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\{JsonResponse, Request};

class CategoryController extends Controller
{
    // ── Tree index ────────────────────────────────────────────────────────

    public function index()
    {
        $roots = Category::with(['allChildren', 'subjects'])
            ->roots()
            ->orderBy('order_index')
            ->get();

        return view('admin.categories.index', compact('roots'));
    }

    // ── AJAX: children + subjects of a node ───────────────────────────────

    public function children(int $id): JsonResponse
    {
        $category = Category::with(['children', 'subjects'])->findOrFail($id);

        return response()->json([
            'children' => $category->children->map(fn ($c) => [
                'id'      => $c->id,
                'name_ar' => $c->name_ar,
                'name_en' => $c->name_en,
                'level'   => $c->level,
            ]),
            'subjects' => $category->subjects->map(fn ($s) => [
                'id'      => $s->id,
                'name_ar' => $s->name_ar,
                'name_en' => $s->name_en,
            ]),
        ]);
    }

    // ── Create / Store ────────────────────────────────────────────────────

    public function create(Request $request)
    {
        $parent     = $request->parent_id ? Category::find($request->parent_id) : null;
        $allCats    = Category::orderBy('level')->orderBy('order_index')->get();

        return view('admin.categories.create', compact('parent', 'allCats'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'parent_id'   => 'nullable|exists:categories,id',
            'name_ar'     => 'required|string|max:200',
            'name_en'     => 'required|string|max:200',
            'icon'        => 'nullable|string|max:100',
            'order_index' => 'nullable|integer|min:0',
            'is_active'   => 'boolean',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        // Derive level from parent
        $data['level'] = $data['parent_id']
            ? (Category::findOrFail($data['parent_id'])->level + 1)
            : 0;

        if ($request->hasFile('image')) {
            $data['image'] = uploadImage('assets/uploads/categories', $request->file('image'));
        }

        Category::create($data);

        return redirect()->route('admin.categories.index')
            ->with('success', __('messages.category_created'));
    }

    // ── Edit / Update ─────────────────────────────────────────────────────

    public function edit(Category $category)
    {
        // Exclude the category itself and its descendants from parent picker
        $selfAndDescendants = $this->collectDescendantIds($category);
        $allCats = Category::whereNotIn('id', $selfAndDescendants)
            ->orderBy('level')->orderBy('order_index')->get();

        return view('admin.categories.edit', compact('category', 'allCats'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'parent_id'   => 'nullable|exists:categories,id',
            'name_ar'     => 'required|string|max:200',
            'name_en'     => 'required|string|max:200',
            'icon'        => 'nullable|string|max:100',
            'order_index' => 'nullable|integer|min:0',
            'is_active'   => 'boolean',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        // Recalculate level if parent changed
        if (isset($data['parent_id']) && $data['parent_id'] != $category->parent_id) {
            $data['level'] = $data['parent_id']
                ? (Category::findOrFail($data['parent_id'])->level + 1)
                : 0;
        }

        if ($request->hasFile('image')) {
            $data['image'] = uploadImage('assets/uploads/categories', $request->file('image'));
        }

        $category->update($data);

        return redirect()->route('admin.categories.index')
            ->with('success', __('messages.category_updated'));
    }

    // ── Delete ────────────────────────────────────────────────────────────

    public function destroy(Category $category)
    {
        // Children's parent_id becomes null due to nullOnDelete FK
        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', __('messages.category_deleted'));
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    private function collectDescendantIds(Category $cat): array
    {
        $ids = [$cat->id];
        foreach ($cat->children as $child) {
            $ids = array_merge($ids, $this->collectDescendantIds($child));
        }
        return $ids;
    }
}
