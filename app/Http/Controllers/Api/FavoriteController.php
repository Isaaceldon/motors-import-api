<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\VehicleResource;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $vehicles = $request->user()
            ->favoriteVehicles()
            ->with(['brand', 'photos'])
            ->latest('favorites.created_at')
            ->get();

        return VehicleResource::collection($vehicles);
    }

    public function store(Request $request, Vehicle $vehicle)
    {
        $request->user()->favoriteVehicles()->syncWithoutDetaching([$vehicle->id]);

        return response()->json(['message' => 'Véhicule ajouté aux favoris.', 'favorited' => true]);
    }

    public function destroy(Request $request, Vehicle $vehicle)
    {
        $request->user()->favoriteVehicles()->detach($vehicle->id);

        return response()->json(['message' => 'Véhicule retiré des favoris.', 'favorited' => false]);
    }
}
