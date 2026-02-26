<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\CreateServiceRequest;
use App\Http\Requests\SuperAdmin\UpdateServiceRequest;
use App\Models\Service;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    use ApiResponse;

    public function store(CreateServiceRequest $request): JsonResponse
    {
        $service = Service::create(array_merge(
            $request->validated(),
            ['created_by' => $request->user()->id]
        ));

        return $this->created($service, 'Service created successfully.');
    }

    public function update(UpdateServiceRequest $request, int $id): JsonResponse
    {
        $service = Service::find($id);

        if (!$service) {
            return $this->error('Service not found.', 404);
        }

        $service->update($request->validated());

        return $this->success($service, 'Service updated successfully.');
    }

    public function destroy(int $id): JsonResponse
    {
        $service = Service::find($id);

        if (!$service) {
            return $this->error('Service not found.', 404);
        }

        if ($service->reservations()->whereNotIn('status', ['cancelled', 'completed'])->exists()) {
            return $this->error('Cannot delete a service with active reservations.', 422);
        }

        $service->delete();

        return $this->noContent('Service deleted successfully.');
    }
}
