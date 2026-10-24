<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SparePartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = $request->query('locale', 'fr');

        $translation = $this->translations->firstWhere('locale', $locale)
            ?? $this->translations->first();

        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'name' => $this->name,
            'condition' => $this->condition,
            'price_usd' => (float) $this->price_usd,
            'price_fcfa' => (float) $this->price_fcfa,
            'stock_quantity' => $this->stock_quantity,
            'online_sale_enabled' => $this->online_sale_enabled,
            'description' => $translation?->description,
            'photos' => $this->photos->pluck('url'),
            'compatibilities' => $this->compatibilities->map(fn ($c) => [
                'brand' => $c->brand->name,
                'model' => $c->model,
                'year_from' => $c->year_from,
                'year_to' => $c->year_to,
            ]),
        ];
    }
}
