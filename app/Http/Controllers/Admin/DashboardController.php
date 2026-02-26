<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Service;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $totalReservations = Reservation::count();

        $todayReservations = Reservation::where('reservation_date', today())->get();

        $totalRevenue = Reservation::where('status', 'completed')
            ->join('services', 'reservations.service_id', '=', 'services.id')
            ->sum('services.price');

        $mostBookedServices = Service::withCount('reservations')
            ->orderBy('reservations_count', 'desc')
            ->limit(5)
            ->get(['id', 'title', 'price', 'reservations_count']);

        $byStatus = Reservation::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        return $this->success([
            'total_reservations'   => $totalReservations,
            'today_reservations'   => $todayReservations->count(),
            'today_details'        => $todayReservations->load(['user', 'service']),
            'total_revenue'        => number_format((float) $totalRevenue, 2),
            'most_booked_services' => $mostBookedServices,
            'reservations_by_status' => $byStatus,
        ]);
    }
}
