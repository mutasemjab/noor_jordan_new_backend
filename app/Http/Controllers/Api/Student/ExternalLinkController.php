<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\ExternalLink;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExternalLinkController extends Controller
{
    use ApiResponse;

    // GET /external-links  [auth]
    public function index(Request $request): JsonResponse
    {
        $student = $request->user();

        $links = ExternalLink::with(['teacher', 'subject'])
            ->where('status', 1)
            ->when($student->class_id, fn ($q) => $q->where(fn ($q) => $q
                ->whereNull('class_id')
                ->orWhere('class_id', $student->class_id)
            ))
            ->when($request->filled('subject_id'), fn ($q) => $q->where('subject_id', $request->subject_id))
            ->latest()
            ->get();

        return $this->success($links->map(fn ($link) => [
            'id'      => $link->id,
            'url'     => $link->url,
            'teacher' => ['id' => $link->teacher?->id, 'name' => $link->teacher?->name],
            'subject' => ['id' => $link->subject?->id, 'name' => $link->subject?->name],
        ])->values());
    }
}
