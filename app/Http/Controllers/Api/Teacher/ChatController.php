<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Admin\FCMController;
use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\ChatMedia;
use App\Models\ClassSubject;
use App\Models\Student;
use App\Services\FirebaseAdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    use ApiResponse;

    // POST /chat/broadcast — teacher sends one message to every student in a class
    public function broadcast(Request $request): JsonResponse
    {
        $teacher = $request->user();

        $data = $request->validate([
            'class_id' => ['required', 'integer', 'exists:classes,id'],
            'text'     => ['nullable', 'string'],
            'media_id' => ['nullable', 'integer', 'exists:chat_media,id'],
        ]);

        if (empty($data['text']) && empty($data['media_id'])) {
            return $this->error('يجب إرسال نص أو وسائط.', 422);
        }

        $teachesClass = ClassSubject::where('teacher_id', $teacher->id)
            ->where('class_id', $data['class_id'])
            ->exists();

        if (! $teachesClass) {
            return $this->error('أنت لا تُدرّس هذا الصف.', 403);
        }

        $media = null;
        if (! empty($data['media_id'])) {
            $media = ChatMedia::find($data['media_id']);

            if (! $media || $media->uploader_type !== 'teacher' || (int) $media->uploader_id !== (int) $teacher->id) {
                return $this->error('الوسائط غير صالحة.', 422);
            }
        }

        $students = Student::where('class_id', $data['class_id'])
            ->where('is_active', true)
            ->get();

        $teacherUid  = 'teacher_' . $teacher->id;
        $messageType = $media?->type ?? 'text';

        $previewText = $data['text'] ?? match ($messageType) {
            'image' => 'صورة',
            'voice' => 'رسالة صوتية',
            default => '',
        };

        $teacherParticipant = [
            'name'   => $teacher->name,
            'avatar' => $teacher->avatar ? asset('assets/uploads/teachers/' . $teacher->avatar) : null,
            'role'   => 'teacher',
        ];

        $sentTo = 0;

        foreach ($students as $student) {
            $studentUid     = 'student_' . $student->id;
            $conversationId = "{$teacherUid}_{$studentUid}";

            $existing = FirebaseAdminService::firestoreGet("conversations/{$conversationId}");
            $unread   = $existing['unreadCount'] ?? [];

            $unread[$studentUid] = (int) ($unread[$studentUid] ?? 0) + 1;
            $unread[$teacherUid] = (int) ($unread[$teacherUid] ?? 0);

            $now = now()->toIso8601String();

            FirebaseAdminService::firestoreSet("conversations/{$conversationId}", [
                'participantIds' => [$teacherUid, $studentUid],
                'participants'   => [
                    $teacherUid => $teacherParticipant,
                    $studentUid => [
                        'name'   => $student->name,
                        'avatar' => $student->avatar ? asset('assets/uploads/students/' . $student->avatar) : null,
                        'role'   => 'student',
                    ],
                ],
                'lastMessage' => [
                    'text'      => $previewText,
                    'type'      => $messageType,
                    'senderId'  => $teacherUid,
                    'createdAt' => $now,
                ],
                'unreadCount' => $unread,
                'updatedAt'   => $now,
            ]);

            $messageFields = [
                'senderId'    => $teacherUid,
                'type'        => $messageType,
                'createdAt'   => $now,
                'readBy'      => [$teacherUid],
                'isBroadcast' => true,
            ];

            if ($messageType === 'text') {
                $messageFields['text'] = $data['text'];
            } else {
                $messageFields['mediaUrl'] = $media->url;
                if ($messageType === 'voice') {
                    $messageFields['mediaDurationSeconds'] = $media->duration_seconds;
                }
                if (! empty($data['text'])) {
                    $messageFields['text'] = $data['text'];
                }
            }

            FirebaseAdminService::firestoreCreate("conversations/{$conversationId}/messages", $messageFields);

            if ($student->fcm_token) {
                FCMController::sendToToken('رسالة من ' . $teacher->name, $previewText, $student->fcm_token, 'chat');
            }

            $sentTo++;
        }

        return $this->success(['sent_to' => $sentTo]);
    }
}
