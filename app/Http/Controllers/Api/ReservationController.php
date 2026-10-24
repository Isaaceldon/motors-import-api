<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Vehicle;
use App\Notifications\NewReservationAdminNotification;
use App\Notifications\ReservationReceivedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class ReservationController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:30'],
            'additional_info' => ['nullable', 'string'],
        ]);

        $vehicle = Vehicle::findOrFail($data['vehicle_id']);

        if ($vehicle->status !== 'disponible') {
            return response()->json([
                'message' => "Ce véhicule n'est plus disponible à la réservation.",
            ], 422);
        }

        $depositPercentage = $vehicle->deposit_percentage_snapshot ?? $vehicle->brand->deposit_percentage;
        $user = $request->user('sanctum');

        $reservation = Reservation::create([
            'vehicle_id' => $vehicle->id,
            'user_id' => $user?->id,
            'customer_name' => $data['customer_name'],
            'customer_email' => $data['customer_email'],
            'customer_phone' => $data['customer_phone'],
            'additional_info' => $data['additional_info'] ?? null,
            'status' => 'en_attente',
            'deposit_amount_usd' => round(($vehicle->price_usd * $depositPercentage) / 100, 2),
            'deposit_amount_fcfa' => round(($vehicle->price_fcfa * $depositPercentage) / 100, 2),
        ]);

        $reservation->load('vehicle.brand');

        if ($user) {
            $user->notify(new ReservationReceivedNotification($reservation));
        } else {
            Notification::route('mail', $reservation->customer_email)
                ->notify(new ReservationReceivedNotification($reservation));
        }

        Notification::send(User::where('role', 'admin')->get(), new NewReservationAdminNotification($reservation));

        return response()->json($reservation->fresh(), 201);
    }

    public function index(Request $request)
    {
        $reservations = Reservation::query()
            ->with('vehicle.brand')
            ->when($request->user() && ! $request->user()->isAdmin(), fn ($q) => $q->where('user_id', $request->user()->id))
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return $reservations;
    }

    public function show(Reservation $reservation)
    {
        return $reservation->load('vehicle.brand', 'payments');
    }
}
