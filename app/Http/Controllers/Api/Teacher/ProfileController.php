<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    use ApiResponse;

    public function show(Request $request): JsonResponse
    {
        $teacher = $request->user();
        $teacher->loadCount('classSubjects');

        return $this->success([
            'id'                   => $teacher->id,
            'name'                 => $teacher->name,
            'email'                => $teacher->email,
            'phone'                => $teacher->phone,
            'gender'               => $teacher->gender,
            'avatar'               => $teacher->avatar
                ? asset('assets/uploads/teachers/' . $teacher->avatar)
                : null,
            'class_subjects_count' => $teacher->class_subjects_count,
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $teacher = $request->user();

        $data = $request->validate([
            'name'     => 'sometimes|string|max:200',
            'phone'    => 'sometimes|nullable|string|max:20',
            'password' => 'sometimes|string|min:8',
        ]);

        $teacher->update($data);

        return $this->success(null, 'تم تحديث البيانات.');
    }
}
