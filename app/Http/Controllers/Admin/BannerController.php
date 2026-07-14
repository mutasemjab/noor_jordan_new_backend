<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('order_index')->get();
        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.banners.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'image'       => 'required|image|mimes:jpg,jpeg,png,webp,gif|max:4096',
            'order_index' => 'nullable|integer|min:0',
        ]);

        $path = uploadImage('assets/uploads/banners', $request->file('image'));

        Banner::create([
            'image'       => $path,
            'order_index' => $request->input('order_index', 0),
            'is_active'   => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.banners.index')
            ->with('success', 'تم إضافة البانر بنجاح.');
    }

    public function edit(Banner $banner)
    {
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:4096',
            'order_index' => 'nullable|integer|min:0',
        ]);

        $data = [
            'order_index' => $request->input('order_index', $banner->order_index),
            'is_active'   => $request->boolean('is_active'),
        ];

        if ($request->hasFile('image')) {
            $data['image'] = uploadImage('assets/uploads/banners', $request->file('image'));
        }

        $banner->update($data);

        return redirect()->route('admin.banners.index')
            ->with('success', 'تم تحديث البانر بنجاح.');
    }

    public function destroy(Banner $banner)
    {
        $banner->delete();
        return back()->with('success', 'تم حذف البانر.');
    }

    public function toggleActive(Banner $banner)
    {
        $banner->update(['is_active' => ! $banner->is_active]);
        return back()->with('success', 'تم تحديث الحالة.');
    }
}
