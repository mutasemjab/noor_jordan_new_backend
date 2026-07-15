<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function edit()
    {
        $teacher = auth()->guard('teacher')->user();

        return view('teacher.profile', compact('teacher'));
    }

    public function update(Request $request)
    {
        $teacher = auth()->guard('teacher')->user();

        $data = $request->validate([
            'name'     => 'required|string|max:200',
            'phone'    => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'avatar'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:1024',
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        if ($request->hasFile('avatar')) {
            $data['avatar'] = uploadImage('public/uploads/teachers', $request->file('avatar'));
        }

        $teacher->update($data);

        return back()->with('success', 'Profile updated successfully.');
    }
}
