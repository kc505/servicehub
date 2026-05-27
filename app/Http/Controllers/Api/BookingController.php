<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\ProviderProfile;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    // Client creates a booking
    public function store(Request $request)
    {
        if (!$request->user()->isClient()) {
            return response()->json(['message' => 'Only clients can make bookings'], 403);
        }

        $request->validate([
            'provider_profile_id' => 'required|exists:provider_profiles,id',
            'service_description' => 'required|string|max:500',
            'scheduled_at'        => 'nullable|date|after:now',
            'client_note'         => 'nullable|string|max:500',
        ]);

        $booking = Booking::create([
            'client_id'           => $request->user()->id,
            'provider_profile_id' => $request->provider_profile_id,
            'service_description' => $request->service_description,
            'scheduled_at'        => $request->scheduled_at,
            'client_note'         => $request->client_note,
            'status'              => 'pending',
        ]);

        return response()->json($booking->load(['client:id,name,email', 'providerProfile']), 201);
    }

    // Get bookings for the authenticated user
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isClient()) {
            $bookings = Booking::with(['providerProfile.user:id,name'])
                ->where('client_id', $user->id)
                ->latest()
                ->get();
        } else {
            $profile  = $user->providerProfile;
            $bookings = Booking::with(['client:id,name,email'])
                ->where('provider_profile_id', $profile->id)
                ->latest()
                ->get();
        }

        return response()->json($bookings);
    }

    // Provider responds to a booking
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status'        => 'required|in:accepted,declined,completed',
            'provider_note' => 'nullable|string|max:500',
        ]);

        $profile = $request->user()->providerProfile;
        $booking = Booking::where('id', $id)
            ->where('provider_profile_id', $profile->id)
            ->firstOrFail();

        $booking->update([
            'status'        => $request->status,
            'provider_note' => $request->provider_note,
        ]);

        return response()->json($booking->load(['client:id,name,email', 'providerProfile']));
    }
}
