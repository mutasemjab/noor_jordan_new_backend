<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function show(int $id)
    {
        $category = Category::active()
            ->with(['subjects' => fn ($query) => $query->withCount('courses')])
            ->findOrFail($id);

        return view('front.category-default', compact('category'));
    }
}
