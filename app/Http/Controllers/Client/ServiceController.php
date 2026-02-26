<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class ServiceController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $services = Service::active()->get();

        return $this->success($services);
    }
}
