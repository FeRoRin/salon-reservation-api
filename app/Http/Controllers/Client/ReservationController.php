<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\CreateReservationRequest;
use App\Models\Reservation;
use App\Services\ReservationService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly ReservationService $reservationService) {}

    public function store(CreateReservationRequest $request): JsonResponse
    {
        $result = $this->reservationService->validateAndCreate(
            array_merge($request->validated(), ['user_id' => $request->user()->id])
        );

        if (isset($result['error'])) {
            return $this->error($result['error'], $result['code']);
        }

        return $this->created($result['reservation'], 'Reservation created successfully.');
    }

    public function myReservations(Request $request): JsonResponse
    {
        $reservations = Reservation::with(['service'])
            ->where('user_id', $request->user()->id)
            ->orderBy('reservation_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->get();

        return $this->success($reservations);
    }

    public function cancel(Request $request, int $id): JsonResponse
    {
        $reservation = Reservation::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$reservation) {
            return $this->error('Reservation not found.', 404);
        }

        if (in_array($reservation->status, ['completed', 'cancelled'])) {
            return $this->error("Cannot cancel a reservation with status: {$reservation->status}.", 422);
        }

        $reservation->update(['status' => 'cancelled']);

        return $this->success($reservation, 'Reservation cancelled successfully.');
    }
}
