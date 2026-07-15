<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiResponse;

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'national_id' => ['required', 'string'],
            'password'    => ['required'],
        ]);

        $student = Student::where('national_id', $request->national_id)->first();

        if (! $student || ! Hash::check($request->password, $student->password)) {
            return $this->error('الرقم الوطني أو كلمة المرور غير صحيحة', 401);
        }

        if (! $student->is_active) {
            return $this->error('الحساب موقوف، تواصل مع الإدارة', 403);
        }

        $token = $student->createToken('student-app')->plainTextToken;

        $student->load(['schoolClass', 'siblings.schoolClass']);

        return $this->success([
            'token'   => $token,
            'student' => $this->studentData($student),
        ]);
    }

    public function switchSibling(Request $request, int $siblingId): JsonResponse
    {
        $current = $request->user();

        // Verify the relationship exists in both directions
        $isSibling = $current->siblings()->where('students.id', $siblingId)->exists();

        if (! $isSibling) {
            return $this->error('هذا الطالب ليس من أفراد عائلتك.', 403);
        }

        $sibling = Student::find($siblingId);

        if (! $sibling || ! $sibling->is_active) {
            return $this->error('الحساب غير متاح.', 403);
        }

        $token = $sibling->createToken('student-app')->plainTextToken;

        $sibling->load(['schoolClass', 'siblings.schoolClass']);

        return $this->success([
            'token'   => $token,
            'student' => $this->studentData($sibling),
        ], 'تم التبديل إلى حساب ' . $sibling->name);
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
            'siblings'    => $student->siblings->map(fn ($s) => [
                'id'     => $s->id,
                'name'   => $s->name,
                'avatar' => $s->avatar ? asset('assets/uploads/students/' . $s->avatar) : null,
                'class'  => $s->schoolClass?->name,
            ])->values(),
        ];
    }
}
