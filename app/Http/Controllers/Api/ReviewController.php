<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Review;
use App\Models\ProviderProfile;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        if (!$request->user()->isClient()) {
            return response()->json(['message' => 'Only clients can leave reviews'], 403);
        }

        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'rating'     => 'required|integer|min:1|max:5',
            'comment'    => 'nullable|string|max:1000',
        ]);

        $booking = Booking::where('id', $request->booking_id)
            ->where('client_id', $request->user()->id)
            ->where('status', 'completed')
            ->firstOrFail();

        if ($booking->review) {
            return response()->json(['message' => 'You have already reviewed this booking'], 422);
        }

        $review = Review::create([
            'booking_id'          => $booking->id,
            'client_id'           => $request->user()->id,
            'provider_profile_id' => $booking->provider_profile_id,
            'rating'              => $request->rating,
            'comment'             => $request->comment,
        ]);

        // Update provider's average rating
        $profile = ProviderProfile::find($booking->provider_profile_id);
        $avg     = Review::where('provider_profile_id', $profile->id)->avg('rating');

        $profile->update([
            'average_rating' => round($avg, 2),
            'total_reviews'  => Review::where('provider_profile_id', $profile->id)->count(),
        ]);

        return response()->json($review->load('client:id,name'), 201);
    }

    public function index($providerProfileId)
    {
        $reviews = Review::with('client:id,name')
            ->where('provider_profile_id', $providerProfileId)
            ->latest()
            ->get();

        return response()->json($reviews);
    }
}
