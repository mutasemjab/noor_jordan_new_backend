<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Teacher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiResponse;

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'national_id'    => ['required', 'string'],
            'password' => ['required'],
        ]);

        $teacher = Teacher::where('national_id', $request->national_id)->first();

        if (! $teacher || ! Hash::check($request->password, $teacher->password)) {
            return $this->error('البريد الإلكتروني أو كلمة المرور غير صحيحة', 401);
        }

        if (! $teacher->is_active) {
            return $this->error('الحساب موقوف، تواصل مع الإدارة', 403);
        }

        $token = $teacher->createToken('teacher-app')->plainTextToken;

        return $this->success([
            'token'   => $token,
            'teacher' => $this->teacherData($teacher),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return $this->success(null, 'تم تسجيل الخروج');
    }

    private function teacherData(Teacher $teacher): array
    {
        return [
            'id'     => $teacher->id,
            'name'   => $teacher->name,
            'national_id'  => $teacher->national_id,
            'phone'  => $teacher->phone,
            'gender' => $teacher->gender,
            'avatar' => $teacher->avatar
                ? asset('assets/uploads/teachers/' . $teacher->avatar)
                : null,
        ];
    }
}
