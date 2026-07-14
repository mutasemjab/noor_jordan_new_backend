<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\StudentNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use ApiResponse;

    // POST /device-token  — save/update student FCM token
    public function saveToken(Request $request): JsonResponse
    {
        $request->validate([
            'fcm_token' => ['required', 'string'],
        ]);

        $request->user()->update(['fcm_token' => $request->fcm_token]);

        return $this->success(null, 'تم حفظ رمز الجهاز');
    }

    // GET /notifications  — list student's notifications
    public function index(Request $request): JsonResponse
    {
        $student = $request->user();

        $notifications = StudentNotification::where('student_id', $student->id)
            ->orderByDesc('created_at')
            ->paginate(20);

        $items = collect($notifications->items())->map(fn ($n) => [
            'id'         => $n->id,
            'title'      => $n->title,
            'body'       => $n->body,
            'type'       => $n->type,
            'data'       => $n->data,
            'is_read'    => $n->is_read,
            'created_at' => $n->created_at->format('Y-m-d H:i'),
        ]);

        $unreadCount = StudentNotification::where('student_id', $student->id)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'status'       => true,
            'message'      => 'OK',
            'unread_count' => $unreadCount,
            'data'         => $items,
            'pagination'   => [
                'current_page' => $notifications->currentPage(),
                'last_page'    => $notifications->lastPage(),
                'per_page'     => $notifications->perPage(),
                'total'        => $notifications->total(),
            ],
        ]);
    }

    // POST /notifications/{id}/read  — mark single notification as read
    public function markRead(Request $request, int $id): JsonResponse
    {
        StudentNotification::where('student_id', $request->user()->id)
            ->where('id', $id)
            ->update(['is_read' => true]);

        return $this->success(null, 'تم التحديث');
    }

    // POST /notifications/read-all  — mark all as read
    public function markAllRead(Request $request): JsonResponse
    {
        StudentNotification::where('student_id', $request->user()->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return $this->success(null, 'تم قراءة جميع الإشعارات');
    }
}
