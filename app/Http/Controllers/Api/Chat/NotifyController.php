<?php

namespace App\Http\Controllers\Api\Chat;

use App\Http\Controllers\Admin\FCMController;
use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\ClassSubject;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotifyController extends Controller
{
    use ApiResponse;

    // POST /notify — push an FCM notification for a new 1-1 chat message
    public function notify(Request $request): JsonResponse
    {
        $data = $request->validate([
            'recipient_type'  => ['required', 'in:student,teacher'],
            'recipient_id'    => ['required', 'integer'],
            'sender_name'     => ['required', 'string'],
            'message_type'    => ['required', 'in:text,image,voice'],
            'message_preview' => ['nullable', 'string'],
        ]);

        $sender = $request->user();

        $recipient = $data['recipient_type'] === 'student'
            ? Student::find($data['recipient_id'])
            : Teacher::find($data['recipient_id']);

        if (! $recipient) {
            return $this->error('المستلم غير موجود.', 404);
        }

        if (! $this->canMessage($sender, $recipient)) {
            return $this->error('لا توجد علاقة صف/مادة تسمح بمراسلة هذا المستخدم.', 403);
        }

        $body = match ($data['message_type']) {
            'image' => 'صورة',
            'voice' => 'رسالة صوتية',
            default => $data['message_preview'] ?? '',
        };

        if ($recipient->fcm_token) {
            FCMController::sendToToken(
                'رسالة جديدة من ' . $data['sender_name'],
                $body,
                $recipient->fcm_token,
                'chat'
            );
        }

        return $this->success(null, 'OK');
    }

    /**
     * A teacher may message a student only if they teach that student's class,
     * and vice versa — mirrors the scoping used in the student "my teachers" list.
     */
    private function canMessage($sender, $recipient): bool
    {
        [$teacher, $student] = $sender instanceof Teacher
            ? [$sender, $recipient]
            : [$recipient, $sender];

        if (! ($teacher instanceof Teacher) || ! ($student instanceof Student)) {
            return false;
        }

        if (! $student->class_id) {
            return false;
        }

        return ClassSubject::where('class_id', $student->class_id)
            ->where('teacher_id', $teacher->id)
            ->exists();
    }
}
