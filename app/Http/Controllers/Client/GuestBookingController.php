<?php
// app/Http/Controllers/Client/GuestBookingController.php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Reservation;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class GuestBookingController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validate all fields
        $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|max:255',
            'phone'            => 'nullable|string|max:20',
            'service_id'       => 'required|exists:services,id',
            'reservation_date' => 'required|date|after_or_equal:today',
            'start_time'       => 'required|date_format:H:i',
            'notes'            => 'nullable|string|max:500',
        ]);

        // 2. Check if email already has an account
        $existingUser = User::where('email', $request->email)->first();
        if ($existingUser) {
            return response()->json([
                'success' => false,
                'message' => 'An account with this email already exists. Please login to book.',
                'should_login' => true,
            ], 409); // 409 Conflict
        }

        // 3. Get service to calculate end_time
        $service = Service::findOrFail($request->service_id);

        // 4. Calculate end time
        $startTime = Carbon::createFromFormat('H:i', $request->start_time);
        $endTime   = $startTime->copy()->addMinutes($service->duration);

        // 5. Check for time overlap
        $overlap = Reservation::where('reservation_date', $request->reservation_date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where(function($q) use ($request, $endTime) {
                $q->whereBetween('start_time', [$request->start_time, $endTime->format('H:i')])
                  ->orWhereBetween('end_time',  [$request->start_time, $endTime->format('H:i')]);
            })->exists();

        if ($overlap) {
            return response()->json([
                'success' => false,
                'message' => 'This time slot is already booked. Please choose a different time.',
            ], 422);
        }

        // 6. Generate a readable password
        // e.g. "Velvet@4829"
        $plainPassword = 'Velvet@' . rand(1000, 9999);

        // 7. Create the user account
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($plainPassword),
            'role'     => 'client',
        ]);

        // 8. Create the reservation
        $reservation = Reservation::create([
            'user_id'          => $user->id,
            'service_id'       => $request->service_id,
            'reservation_date' => $request->reservation_date,
            'start_time'       => $request->start_time,
            'end_time'         => $endTime->format('H:i'),
            'notes'            => $request->notes,
            'status'           => 'pending',
        ]);

        // 9. Send password by email
        // We use a simple Mail::raw() — no blade template needed
        try {
            Mail::raw(
                "Hello {$user->name},\n\n" .
                "Your Velvet Salon booking is confirmed!\n\n" .
                "Booking Details:\n" .
                "• Service: {$service->title}\n" .
                "• Date: {$reservation->reservation_date}\n" .
                "• Time: {$reservation->start_time}\n\n" .
                "Your account has been created:\n" .
                "• Email: {$user->email}\n" .
                "• Password: {$plainPassword}\n\n" .
                "You can login at: http://localhost:5173/login\n" .
                "and change your password from your profile.\n\n" .
                "Thank you for choosing Velvet Salon 🌸",
                function ($message) use ($user) {
                    $message->to($user->email, $user->name)
                            ->subject('Your Velvet Salon Booking & Account Details');
                }
            );
        } catch (\Exception $e) {
            // Email failed — don't crash the booking, just log it
            \Log::warning('Guest booking email failed: ' . $e->getMessage());
        }

        // 10. Return everything including plain password (show it on screen)
        return response()->json([
            'success' => true,
            'message' => 'Booking confirmed! Your account has been created.',
            'data' => [
                'reservation' => [
                    'id'               => $reservation->id,
                    'service'          => $service->title,
                    'reservation_date' => $reservation->reservation_date,
                    'start_time'       => $reservation->start_time,
                    'end_time'         => $reservation->end_time,
                    'status'           => $reservation->status,
                ],
                'account' => [
                    'name'     => $user->name,
                    'email'    => $user->email,
                    'password' => $plainPassword, // Plain text — shown once on screen
                ],
            ],
        ], 201);
    }
}