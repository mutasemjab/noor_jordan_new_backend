<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    use ApiResponse;

    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:200'],
            'national_id' => ['required', 'string', 'max:50', 'unique:students,national_id'],
            'email'       => ['nullable', 'email', 'max:200', 'unique:students,email'],
            'phone'       => ['nullable', 'string', 'max:20'],
            'password'    => ['required', 'confirmed', Password::min(8)],
            'class_id'    => ['nullable', 'exists:classes,id'],
            'deviceId' => ['required', 'string', 'max:36', 'unique:students,deviceId'],
        ]);

        $student = Student::create([
            'name'        => $validated['name'],
            'national_id' => $validated['national_id'],
            'email'       => $validated['email'] ?? null,
            'phone'       => $validated['phone'] ?? null,
            'password'    => $validated['password'],
            'class_id'    => $validated['class_id'] ?? null,
            'deviceId' => $validated['deviceId'],
            'is_active'   => true,
        ]);

        $token = $student->createToken('student-app')->plainTextToken;

        return $this->success([
            'token'   => $token,
            'student' => $this->studentData($student),
        ], 'تم التسجيل بنجاح', 201);
    }

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'national_id' => ['required', 'string'],
            'password'    => ['required'],
            'deviceId' => ['required', 'string', 'max:36'],
        ]);

        $student = Student::where('national_id', $request->national_id)->first();

        if (! $student || ! Hash::check($request->password, $student->password)) {
            return $this->error('الرقم الوطني أو كلمة المرور غير صحيحة', 401);
        }

        if (! $student->is_active) {
            return $this->error('الحساب موقوف، تواصل مع الإدارة', 403);
        }

        // // Device lock: if student already has a uuid, it must match
        // if ($student->deviceId && $student->deviceId !== $request->deviceId) {
        //     return $this->error('هذا الحساب مسجّل على جهاز آخر، لا يمكن تسجيل الدخول من جهاز مختلف. تواصل مع الإدارة لإعادة تعيين الجهاز.', 403);
        // }

        // First login after migration: save the uuid
        if (! $student->deviceId) {
            $student->update(['deviceId' => $request->deviceId]);
        }

        $token = $student->createToken('student-app')->plainTextToken;

        return $this->success([
            'token'   => $token,
            'student' => $this->studentData($student->load('schoolClass')),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return $this->success(null, 'تم تسجيل الخروج');
    }

    public function deleteAccount(Request $request): JsonResponse
    {
        $student = $request->user();

        $student->update(['is_active' => false]);
        $student->tokens()->delete();

        return $this->success(null, 'تم حذف الحساب بنجاح');
    }

    private function studentData(Student $student): array
    {
        return [
            'id'          => $student->id,
            'name'        => $student->name,
            'national_id' => $student->national_id,
            'email'       => $student->email,
            'phone'       => $student->phone,
            'avatar'      => $student->avatar ? asset('assets/uploads/students/' . $student->avatar) : null,
            'class'       => $student->schoolClass?->name,
            'class_id'    => $student->class_id,
            'gender'      => $student->gender,
            'is_active'   => $student->is_active,
        ];
    }
}
