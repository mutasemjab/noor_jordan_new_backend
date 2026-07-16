<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Announcement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    use ApiResponse;

    // GET /v1/teacher/announcements
    public function index(): JsonResponse
    {
        $announcements = Announcement::active()
            ->whereNull('class_id')          // global announcements only for teachers
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->paginate(20);

        $items = collect($announcements->items())->map(fn ($a) => [
            'id'           => $a->id,
            'title'        => $a->title,
            'body'         => $a->body,
            'image'        => $a->image ? asset('assets/uploads/announcements/' . $a->image) : null,
            'published_at' => $a->published_at?->format('Y-m-d H:i') ?? $a->created_at->format('Y-m-d H:i'),
        ]);

        return response()->json([
            'status'     => true,
            'message'    => 'OK',
            'data'       => $items,
            'pagination' => [
                'current_page' => $announcements->currentPage(),
                'last_page'    => $announcements->lastPage(),
                'per_page'     => $announcements->perPage(),
                'total'        => $announcements->total(),
            ],
        ]);
    }

    // GET /v1/teacher/announcements/{id}
    public function show(int $id): JsonResponse
    {
        $announcement = Announcement::active()->whereNull('class_id')->findOrFail($id);

        return $this->success([
            'id'           => $announcement->id,
            'title'        => $announcement->title,
            'body'         => $announcement->body,
            'image'        => $announcement->image ? asset('assets/uploads/announcements/' . $announcement->image) : null,
            'published_at' => $announcement->published_at?->format('Y-m-d H:i') ?? $announcement->created_at->format('Y-m-d H:i'),
        ]);
    }
}
