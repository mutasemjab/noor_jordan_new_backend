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

        $items = collect($announcements->items())->map(fn ($a) => array_merge([
            'id'           => $a->id,
            'title'        => $a->title,
            'body'         => $a->body,
            'class_id'     => $a->class_id,
            'published_at' => $a->published_at?->format('Y-m-d H:i') ?? $a->created_at->format('Y-m-d H:i'),
        ], $this->attachment($a)));

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
            ->forStudent($student)
            ->findOrFail($id);

        return $this->success(array_merge([
            'id'           => $announcement->id,
            'title'        => $announcement->title,
            'body'         => $announcement->body,
            'class_id'     => $announcement->class_id,
            'published_at' => $announcement->published_at?->format('Y-m-d H:i') ?? $announcement->created_at->format('Y-m-d H:i'),
        ], $this->attachment($announcement)));
    }

    // `image` keeps its historical meaning exactly (every pre-existing row has
    // attachment_type=null and is a real image, so null/'image' both count as
    // an image) - `pdf_url` is new and only set for the new PDF case.
    private function attachment(Announcement $a): array
    {
        $url = $a->image ? asset('assets/uploads/announcements/' . $a->image) : null;
        $isPdf = $a->attachment_type === 'pdf';

        return [
            'image'   => $isPdf ? null : $url,
            'pdf_url' => $isPdf ? $url : null,
        ];
    }
}
