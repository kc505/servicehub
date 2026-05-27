<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProviderProfile;
use Illuminate\Http\Request;

class ServiceDirectoryController extends Controller
{
    public function index(Request $request)
    {
        $query = ProviderProfile::with(['user:id,name,email', 'portfolioImages'])
            ->where('is_available', true);

        if ($request->filled('category')) {
            $query->where('trade_category', 'like', '%' . $request->category . '%');
        }

        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        if ($request->filled('min_rating')) {
            $query->where('average_rating', '>=', $request->min_rating);
        }

        $providers = $query->orderBy('average_rating', 'desc')->paginate(10);

        return response()->json($providers);
    }

    public function show($id)
    {
        $profile = ProviderProfile::with([
            'user:id,name,email',
            'portfolioImages',
            'reviews.client:id,name',
        ])->findOrFail($id);

        return response()->json($profile);
    }
}
