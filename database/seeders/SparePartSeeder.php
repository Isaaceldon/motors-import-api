<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\SparePart;
use Illuminate\Database\Seeder;

class SparePartSeeder extends Seeder
{
    private const USD_TO_FCFA = 612;

    public function run(): void
    {
        $parts = [
            [
                'reference' => 'BMW-BRK-001', 'name' => 'Plaquettes de frein avant',
                'condition' => 'neuve', 'price_usd' => 85, 'stock_quantity' => 24,
                'online_sale_enabled' => true, 'brand' => 'BMW', 'model' => 'Série 5',
                'description' => 'Plaquettes de frein avant compatibles BMW Série 5, qualité constructeur.',
            ],
            [
                'reference' => 'TOY-ALT-014', 'name' => 'Alternateur',
                'condition' => 'occasion', 'price_usd' => 120, 'stock_quantity' => 3,
                'online_sale_enabled' => false, 'brand' => 'Toyota', 'model' => 'Camry',
                'description' => 'Alternateur reconditionné pour Toyota Camry, testé et garanti.',
            ],
            [
                'reference' => 'MER-PHR-022', 'name' => 'Phare avant droit',
                'condition' => 'neuve', 'price_usd' => 240, 'stock_quantity' => 10,
                'online_sale_enabled' => true, 'brand' => 'Mercedes-Benz', 'model' => 'GLE',
                'description' => 'Phare avant droit d\'origine pour Mercedes GLE.',
            ],
        ];

        foreach ($parts as $data) {
            $brand = Brand::where('name', $data['brand'])->first();

            $part = SparePart::create([
                'reference' => $data['reference'],
                'name' => $data['name'],
                'condition' => $data['condition'],
                'price_usd' => $data['price_usd'],
                'price_fcfa' => $data['price_usd'] * self::USD_TO_FCFA,
                'stock_quantity' => $data['stock_quantity'],
                'online_sale_enabled' => $data['online_sale_enabled'],
            ]);

            $part->translations()->create([
                'locale' => 'fr',
                'description' => $data['description'],
            ]);

            $part->compatibilities()->create([
                'brand_id' => $brand->id,
                'model' => $data['model'],
            ]);
        }
    }
}
