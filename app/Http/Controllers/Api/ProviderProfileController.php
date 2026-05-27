<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PortfolioImage;
use App\Models\ProviderProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProviderProfileController extends Controller
{
    // Get the authenticated provider's profile
    public function show(Request $request)
    {
        $profile = $request->user()->providerProfile()->with('portfolioImages')->first();

        if (!$profile) {
            return response()->json(['message' => 'Profile not found'], 404);
        }

        return response()->json($profile);
    }

    // Create or update the provider's profile
    public function upsert(Request $request)
    {
        if (!$request->user()->isProvider()) {
            return response()->json(['message' => 'Only service providers can manage profiles'], 403);
        }

        $request->validate([
            'business_name' => 'nullable|string|max:255',
            'trade_category' => 'required|string|max:255',
            'bio'           => 'nullable|string',
            'location'      => 'required|string|max:255',
            'phone'         => 'nullable|string|max:20',
            'hourly_rate'   => 'nullable|numeric|min:0',
            'is_available'  => 'nullable|boolean',
        ]);

        $profile = ProviderProfile::updateOrCreate(
            ['user_id' => $request->user()->id],
            $request->only([
                'business_name',
                'trade_category',
                'bio',
                'location',
                'phone',
                'hourly_rate',
                'is_available',
            ])
        );

        return response()->json($profile->load('portfolioImages'), 200);
    }

    // Upload a portfolio image
    public function uploadImage(Request $request)
    {
        if (!$request->user()->isProvider()) {
            return response()->json(['message' => 'Only service providers can upload images'], 403);
        }

        $request->validate([
            'image'   => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'caption' => 'nullable|string|max:255',
        ]);

        $profile = $request->user()->providerProfile;

        if (!$profile) {
            return response()->json(['message' => 'Create your profile first'], 404);
        }

        $path = $request->file('image')->store('portfolio', 'public');

        $image = PortfolioImage::create([
            'provider_profile_id' => $profile->id,
            'image_path'          => $path,
            'caption'             => $request->caption,
        ]);

        return response()->json($image, 201);
    }

    // Delete a portfolio image
    public function deleteImage(Request $request, $imageId)
    {
        $profile = $request->user()->providerProfile;
        $image   = PortfolioImage::where('id', $imageId)
            ->where('provider_profile_id', $profile->id)
            ->firstOrFail();

        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        return response()->json(['message' => 'Image deleted']);
    }
}
