<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\UpdateBusinessSettingsRequest;
use App\Models\BusinessSetting;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class BusinessSettingsController extends Controller
{
    use ApiResponse;

    public function update(UpdateBusinessSettingsRequest $request): JsonResponse
    {
        $settings = BusinessSetting::current();
        $settings->update($request->validated());

        return $this->success($settings, 'Business settings updated successfully.');
    }

    public function show(): JsonResponse
    {
        return $this->success(BusinessSetting::current());
    }
}
