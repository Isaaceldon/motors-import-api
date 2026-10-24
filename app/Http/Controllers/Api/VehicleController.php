<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\VehicleDetailResource;
use App\Http\Resources\VehicleResource;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $query = Vehicle::query()->with(['brand', 'photos']);

        $query->when($request->filled('brand'), fn ($q) => $q->whereHas(
            'brand',
            fn ($b) => $b->where('name', $request->query('brand'))
        ));

        $query->when($request->filled('model'), fn ($q) => $q->where(
            'model',
            'like',
            '%'.$request->query('model').'%'
        ));

        $query->when($request->filled('q'), function ($q) use ($request) {
            $keyword = '%'.$request->query('q').'%';

            $q->where(function ($sub) use ($keyword) {
                $sub->where('model', 'like', $keyword)
                    ->orWhere('version', 'like', $keyword)
                    ->orWhereHas('brand', fn ($b) => $b->where('name', 'like', $keyword));
            });
        });

        $query->when($request->filled('year'), fn ($q) => $q->where('year', $request->query('year')));

        $query->when($request->filled('condition'), fn ($q) => $q->where('condition', $request->query('condition')));
        $query->when($request->filled('status'), fn ($q) => $q->where('status', $request->query('status')));
        $query->when($request->filled('min_price'), fn ($q) => $q->where('price_usd', '>=', $request->query('min_price')));
        $query->when($request->filled('max_price'), fn ($q) => $q->where('price_usd', '<=', $request->query('max_price')));
        $query->when($request->filled('max_mileage'), fn ($q) => $q->where('mileage', '<=', $request->query('max_mileage')));
        $query->when($request->filled('fuel_type'), fn ($q) => $q->where('fuel_type', $request->query('fuel_type')));
        $query->when($request->filled('body_style'), fn ($q) => $q->where('body_style', $request->query('body_style')));
        $query->when($request->boolean('featured'), fn ($q) => $q->where('is_featured', true));

        $sort = $request->query('sort', 'recent');
        match ($sort) {
            'price_asc' => $query->orderBy('price_usd', 'asc'),
            'price_desc' => $query->orderBy('price_usd', 'desc'),
            'mileage' => $query->orderBy('mileage', 'asc'),
            'year' => $query->orderBy('year', 'desc'),
            default => $query->orderBy('created_at', 'desc'),
        };

        $vehicles = $query->paginate($request->integer('per_page', 12));

        return VehicleResource::collection($vehicles);
    }

    public function show(Request $request, Vehicle $vehicle)
    {
        $vehicle->load(['brand', 'translations', 'photos', 'features']);

        return new VehicleDetailResource($vehicle);
    }

    public function similar(Vehicle $vehicle)
    {
        $similar = Vehicle::query()
            ->with(['brand', 'photos'])
            ->where('id', '!=', $vehicle->id)
            ->where('brand_id', $vehicle->brand_id)
            ->where('status', 'disponible')
            ->limit(4)
            ->get();

        return VehicleResource::collection($similar);
    }
}
