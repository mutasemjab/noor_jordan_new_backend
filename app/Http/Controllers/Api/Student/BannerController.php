<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Banner;
use Illuminate\Http\JsonResponse;

class BannerController extends Controller
{
    use ApiResponse;

    // GET /banners  — returns all active banners ordered by order_index
    public function index(): JsonResponse
    {
        $banners = Banner::active()->get()->map(fn ($b) => [
            'id'          => $b->id,
            'image'       => asset('assets/uploads/banners/' . $b->image),
            'order_index' => $b->order_index,
        ]);

        return $this->success($banners);
    }
}
