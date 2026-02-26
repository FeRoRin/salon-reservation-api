<?php

namespace App\Services;

use App\Models\BusinessSetting;
use App\Models\Reservation;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class ReservationService
{
    public function validateAndCreate(array $data, bool $createdByAdmin = false): array
    {
        $service  = Service::findOrFail($data['service_id']);
        $settings = BusinessSetting::current();

        $reservationDate = Carbon::parse($data['reservation_date']);
        $dayOfWeek = (int) $reservationDate->format('w'); // 0=Sunday, 6=Saturday

        // 1. Check working day
        if (!in_array($dayOfWeek, $settings->working_days)) {
            return ['error' => 'The selected date is not a working day.', 'code' => 422];
        }

        $startTime = $data['start_time'];
        $endTime   = Carbon::parse($data['reservation_date'] . ' ' . $startTime)
            ->addMinutes($service->duration)
            ->format('H:i:s');

        // 2. Check time within business hours
        $openTime  = $settings->open_time;
        $closeTime = $settings->close_time;

        if ($startTime . ':00' < $openTime || $endTime > $closeTime) {
            return [
                'error' => "Reservation must be between {$openTime} and {$closeTime}.",
                'code'  => 422,
            ];
        }

        // 3. Check overlapping reservations
        $overlap = Reservation::where('reservation_date', $data['reservation_date'])
            ->whereNotIn('status', ['cancelled'])
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where(function ($q) use ($startTime, $endTime) {
                    $q->where('start_time', '<', $endTime)
                      ->where('end_time', '>', $startTime . ':00');
                });
            })
            ->exists();

        if ($overlap) {
            return ['error' => 'This time slot is already booked. Please choose another time.', 'code' => 409];
        }

        $reservation = Reservation::create([
            'user_id'          => $data['user_id'],
            'service_id'       => $data['service_id'],
            'reservation_date' => $data['reservation_date'],
            'start_time'       => $startTime,
            'end_time'         => $endTime,
            'status'           => 'pending',
            'notes'            => $data['notes'] ?? null,
            'created_by_admin' => $createdByAdmin,
        ]);

        return ['reservation' => $reservation->load(['user', 'service'])];
    }
}
