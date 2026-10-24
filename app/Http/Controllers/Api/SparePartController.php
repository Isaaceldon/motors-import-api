<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SparePartResource;
use App\Models\SparePart;
use Illuminate\Http\Request;

class SparePartController extends Controller
{
    public function index(Request $request)
    {
        $query = SparePart::query()->with(['translations', 'photos', 'compatibilities.brand']);

        $query->when($request->filled('condition'), fn ($q) => $q->where('condition', $request->query('condition')));

        $query->when($request->filled('brand'), fn ($q) => $q->whereHas(
            'compatibilities.brand',
            fn ($b) => $b->where('name', $request->query('brand'))
        ));

        $query->when($request->filled('min_price'), fn ($q) => $q->where('price_usd', '>=', $request->query('min_price')));
        $query->when($request->filled('max_price'), fn ($q) => $q->where('price_usd', '<=', $request->query('max_price')));

        $query->when($request->filled('q'), function ($q) use ($request) {
            $keyword = '%'.$request->query('q').'%';

            $q->where(function ($sub) use ($keyword) {
                $sub->where('name', 'like', $keyword)
                    ->orWhere('reference', 'like', $keyword);
            });
        });

        $sort = $request->query('sort', 'recent');
        match ($sort) {
            'price_asc' => $query->orderBy('price_usd', 'asc'),
            'price_desc' => $query->orderBy('price_usd', 'desc'),
            'name' => $query->orderBy('name', 'asc'),
            default => $query->orderBy('created_at', 'desc'),
        };

        $parts = $query->paginate($request->integer('per_page', 12));

        return SparePartResource::collection($parts);
    }

    public function show(Request $request, SparePart $sparePart)
    {
        $sparePart->load(['translations', 'photos', 'compatibilities.brand']);

        return new SparePartResource($sparePart);
    }
}
