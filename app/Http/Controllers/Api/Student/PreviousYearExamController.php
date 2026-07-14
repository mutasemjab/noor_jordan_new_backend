<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\PreviousYearExam;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PreviousYearExamController extends Controller
{
    use ApiResponse;

    // GET /previous-year-exams
    public function index(Request $request): JsonResponse
    {
        $query = PreviousYearExam::with(['subject', 'teacher'])
            ->where('status', 1)
            ->orderBy('sort_order');

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }
        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn ($q) => $q
                ->where('title_ar', 'like', "%{$s}%")
                ->orWhere('title_en', 'like', "%{$s}%")
            );
        }

        $paginated = $query->paginate(15);

        return response()->json([
            'status'     => true,
            'message'    => 'OK',
            'data'       => collect($paginated->items())->map(fn ($p) => $this->itemData($p)),
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'last_page'    => $paginated->lastPage(),
                'per_page'     => $paginated->perPage(),
                'total'        => $paginated->total(),
            ],
        ]);
    }

    // GET /previous-year-exams/{id}
    public function show(int $id): JsonResponse
    {
        $item = PreviousYearExam::with(['subject', 'teacher'])
            ->where('status', 1)
            ->findOrFail($id);

        return $this->success($this->itemData($item));
    }

    private function itemData(PreviousYearExam $item): array
    {
        return [
            'id'        => $item->id,
            'title'     => $item->title,
            'title_ar'  => $item->title_ar,
            'title_en'  => $item->title_en,
            'tag'       => $item->tag,
            'year'      => $item->year,
            'pages'     => $item->pages,
            'file_size' => $item->file_size,
            'pdf_url'   => $item->pdf_file ? asset('assets/uploads/previousYearExam/' . $item->pdf_file) : null,
            'subject'   => ['id' => $item->subject?->id, 'name' => $item->subject?->name],
            'teacher'   => ['id' => $item->teacher?->id, 'name' => $item->teacher?->name],
        ];
    }
}
