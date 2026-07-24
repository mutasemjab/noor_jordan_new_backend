<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Services\FirebaseAdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FirebaseTokenController extends Controller
{
    use ApiResponse;

    // GET /firebase-token
    public function show(Request $request): JsonResponse
    {
        $teacher = $request->user();

        try {
            $token = FirebaseAdminService::createCustomToken('teacher_' . $teacher->id);
        } catch (\Throwable $e) {
            return $this->error('تعذر إصدار رمز الدردشة حالياً.', 500);
        }

        return $this->success(['firebase_token' => $token]);
    }
}
