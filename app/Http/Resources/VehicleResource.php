<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'brand' => $this->brand->name,
            'model' => $this->model,
            'version' => $this->version,
            'year' => $this->year,
            'condition' => $this->condition,
            'status' => $this->status,
            'price_usd' => (float) $this->price_usd,
            'price_fcfa' => (float) $this->price_fcfa,
            'mileage' => $this->mileage,
            'fuel_type' => $this->fuel_type,
            'transmission' => $this->transmission,
            'body_style' => $this->body_style,
            'is_featured' => $this->is_featured,
            'cover_photo' => optional($this->photos->first())->url,
            'is_favorited' => $request->user('sanctum')
                ? $request->user('sanctum')->favoriteVehicles()->where('vehicle_id', $this->id)->exists()
                : false,
        ];
    }
}
