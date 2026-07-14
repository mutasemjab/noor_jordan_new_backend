<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\POS;
use Illuminate\Http\Request;

class PosController extends Controller
{
    public function index(Request $request)
    {
        $pos = POS::with('city')
            ->withCount('cards')
            ->when($request->search, fn ($q, $s) => $q
                ->where('name_ar', 'like', "%{$s}%")
                ->orWhere('name_en', 'like', "%{$s}%")
                ->orWhere('phone',   'like', "%{$s}%")
            )
            ->when($request->city_id, fn ($q, $c) => $q->where('city_id', $c))
            ->orderBy('name_en')
            ->paginate(15)
            ->withQueryString();

        $cities = City::orderBy('name_en')->get();

        return view('admin.pos.index', compact('pos', 'cities'));
    }

    public function create()
    {
        $cities = City::orderBy('name_en')->get();

        return view('admin.pos.create', compact('cities'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'city_id'         => 'required|exists:cities,id',
            'name_ar'         => 'required|string|max:200',
            'name_en'         => 'required|string|max:200',
            'phone'           => 'required|string|max:50',
            'google_map_link' => 'nullable|string|max:2000',
        ]);

        POS::create($data);

        return redirect()->route('admin.pos.index')
            ->with('success', 'Point of sale created successfully.');
    }

    public function edit(POS $po)
    {
        $cities = City::orderBy('name_en')->get();

        return view('admin.pos.edit', compact('po', 'cities'));
    }

    public function update(Request $request, POS $po)
    {
        $data = $request->validate([
            'city_id'         => 'required|exists:cities,id',
            'name_ar'         => 'required|string|max:200',
            'name_en'         => 'required|string|max:200',
            'phone'           => 'required|string|max:50',
            'google_map_link' => 'nullable|string|max:2000',
        ]);

        $po->update($data);

        return redirect()->route('admin.pos.index')
            ->with('success', 'Point of sale updated successfully.');
    }

    public function destroy(POS $po)
    {
        $po->delete();

        return redirect()->route('admin.pos.index')
            ->with('success', 'Point of sale deleted successfully.');
    }
}
