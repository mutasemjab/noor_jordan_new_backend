<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bus;
use Illuminate\Http\Request;

class BusController extends Controller
{
    public function index(Request $request)
    {
        $buses = Bus::withCount('trips')
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.buses.index', compact('buses'));
    }

    public function create()
    {
        return view('admin.buses.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:100',
            'capacity'  => 'required|integer|min:1|max:200',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        Bus::create($data);

        return redirect()->route('admin.buses.index')
            ->with('success', 'تم إضافة الباص بنجاح.');
    }

    public function edit(Bus $bus)
    {
        return view('admin.buses.edit', compact('bus'));
    }

    public function update(Request $request, Bus $bus)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:100',
            'capacity'  => 'required|integer|min:1|max:200',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $bus->update($data);

        return redirect()->route('admin.buses.index')
            ->with('success', 'تم تحديث بيانات الباص.');
    }

    public function destroy(Bus $bus)
    {
        $bus->delete();

        return redirect()->route('admin.buses.index')
            ->with('success', 'تم حذف الباص.');
    }
}
