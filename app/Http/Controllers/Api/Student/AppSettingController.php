<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\SiteSetting;
use Illuminate\Http\JsonResponse;

class AppSettingController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $showPrice = (int) (SiteSetting::raw('show_price') ?: '1');

        return $this->success([
            'show_price' => $showPrice,
        ]);
    }
}
