<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateReservationRequest;
use App\Http\Requests\Admin\UpdateReservationRequest;
use App\Models\Reservation;
use App\Services\ReservationService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly ReservationService $reservationService) {}

    public function index(Request $request): JsonResponse
    {
        $query = Reservation::with(['user', 'service']);

        // Search by client name
        if ($request->filled('client_name')) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', '%' . $request->client_name . '%'));
        }

        // Search by phone
        if ($request->filled('phone')) {
            $query->whereHas('user', fn($q) => $q->where('phone', 'like', '%' . $request->phone . '%'));
        }

        // Filter by date
        if ($request->filled('date')) {
            $query->where('reservation_date', $request->date);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Sort
        $sortOrder = in_array($request->sort, ['asc', 'desc']) ? $request->sort : 'desc';
        $query->orderBy('reservation_date', $sortOrder)->orderBy('start_time', $sortOrder);

        return $this->success($query->paginate(15));
    }

    public function store(CreateReservationRequest $request): JsonResponse
    {
        $result = $this->reservationService->validateAndCreate($request->validated(), true);

        if (isset($result['error'])) {
            return $this->error($result['error'], $result['code']);
        }

        return $this->created($result['reservation'], 'Reservation created successfully.');
    }

    public function update(UpdateReservationRequest $request, int $id): JsonResponse
    {
        $reservation = Reservation::find($id);

        if (!$reservation) {
            return $this->error('Reservation not found.', 404);
        }

        $reservation->update($request->validated());

        return $this->success($reservation->load(['user', 'service']), 'Reservation updated.');
    }
}
