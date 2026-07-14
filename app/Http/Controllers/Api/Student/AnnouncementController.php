<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Announcement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    use ApiResponse;

    // GET /announcements  (filtered by student's class or global)
    public function index(Request $request): JsonResponse
    {
        $student = $request->user();

        $announcements = Announcement::active()
            ->forStudent($student)
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->paginate(20);

        $items = collect($announcements->items())->map(fn ($a) => [
            'id'           => $a->id,
            'title'        => $a->title,
            'body'         => $a->body,
            'image'        => $a->image ? asset('assets/uploads/announcements/' . $a->image) : null,
            'class_id'     => $a->class_id,
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

    // GET /announcements/{id}
    public function show(Request $request, int $id): JsonResponse
    {
        $student = $request->user();

        $announcement = Announcement::active()
            ->where(function ($q) use ($student) {
                $q->whereNull('class_id')->orWhere('class_id', $student->class_id);
            })
            ->findOrFail($id);

        return $this->success([
            'id'           => $announcement->id,
            'title'        => $announcement->title,
            'body'         => $announcement->body,
            'image'        => $announcement->image ? asset('assets/uploads/announcements/' . $announcement->image) : null,
            'class_id'     => $announcement->class_id,
            'published_at' => $announcement->published_at?->format('Y-m-d H:i') ?? $announcement->created_at->format('Y-m-d H:i'),
        ]);
    }
}
