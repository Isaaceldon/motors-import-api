<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = $request->query('locale', 'fr');

        $translation = $this->translations->firstWhere('locale', $locale)
            ?? $this->translations->first();

        return [
            'id' => $this->id,
            'brand' => new BrandResource($this->whenLoaded('brand', $this->brand)),
            'model' => $this->model,
            'version' => $this->version,
            'year' => $this->year,
            'condition' => $this->condition,
            'status' => $this->status,
            'price_usd' => (float) $this->price_usd,
            'price_fcfa' => (float) $this->price_fcfa,
            'deposit_percentage' => $this->deposit_percentage_snapshot,
            'deposit_amount_usd' => round(($this->price_usd * $this->deposit_percentage_snapshot) / 100, 2),
            'deposit_amount_fcfa' => round(($this->price_fcfa * $this->deposit_percentage_snapshot) / 100, 2),
            'mileage' => $this->mileage,
            'fuel_type' => $this->fuel_type,
            'transmission' => $this->transmission,
            'engine' => $this->engine,
            'color' => $this->color,
            'doors' => $this->doors,
            'seats' => $this->seats,
            'body_style' => $this->body_style,
            'vin' => $this->vin ? substr($this->vin, 0, 4).str_repeat('•', max(strlen($this->vin) - 4, 0)) : null,
            'description' => $translation?->description,
            'features' => $this->features->pluck('name'),
            'photos' => $this->photos->pluck('url'),
            'is_featured' => $this->is_featured,
            'is_favorited' => $request->user('sanctum')
                ? $request->user('sanctum')->favoriteVehicles()->where('vehicle_id', $this->id)->exists()
                : false,
        ];
    }
}
