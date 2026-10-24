<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Feature;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    private const USD_TO_FCFA = 612;

    public function run(): void
    {
        $vehicles = [
            [
                'brand' => 'BMW', 'model' => 'M5', 'version' => 'Competition Sedan AWD',
                'year' => 2022, 'condition' => 'occasion', 'status' => 'disponible',
                'price_usd' => 68500, 'mileage' => 18400, 'fuel_type' => 'Essence',
                'transmission' => 'Automatique', 'engine' => 'V8 4.4L Bi-Turbo', 'color' => 'Noir',
                'doors' => 4, 'seats' => 5, 'body_style' => 'Berline', 'is_featured' => true,
                'features' => ['Climatisation', 'GPS', 'Sièges cuir', 'Toit ouvrant', 'Sièges chauffants', 'Jantes alliage'],
                'description' => "BMW M5 Competition en excellent état, entretien suivi, aucun accident.",
            ],
            [
                'brand' => 'Toyota', 'model' => 'Camry', 'version' => 'XSE V6',
                'year' => 2021, 'condition' => 'occasion', 'status' => 'reserve',
                'price_usd' => 21900, 'mileage' => 32100, 'fuel_type' => 'Essence',
                'transmission' => 'Automatique', 'engine' => 'V6 3.5L', 'color' => 'Blanc',
                'doors' => 4, 'seats' => 5, 'body_style' => 'Berline', 'is_featured' => true,
                'features' => ['Climatisation', 'Caméra de recul', 'Bluetooth', 'Régulateur de vitesse'],
                'description' => "Toyota Camry fiable et économique, idéale pour un usage familial.",
            ],
            [
                'brand' => 'Mercedes-Benz', 'model' => 'GLE 450', 'version' => '4MATIC SUV',
                'year' => 2023, 'condition' => 'occasion', 'status' => 'disponible',
                'price_usd' => 54900, 'mileage' => 9800, 'fuel_type' => 'Essence',
                'transmission' => 'Automatique', 'engine' => 'I6 3.0L Turbo', 'color' => 'Gris',
                'doors' => 5, 'seats' => 5, 'body_style' => 'SUV', 'is_featured' => true,
                'features' => ['Climatisation', 'GPS', 'Sièges cuir', 'Caméra de recul', 'Système audio premium', 'Aide au stationnement'],
                'description' => "Mercedes GLE 450 récente, faible kilométrage, garantie constructeur active.",
            ],
            [
                'brand' => 'Ford', 'model' => 'F-150', 'version' => 'Raptor',
                'year' => 2022, 'condition' => 'occasion', 'status' => 'vendu',
                'price_usd' => 58700, 'mileage' => 24500, 'fuel_type' => 'Essence',
                'transmission' => 'Automatique', 'engine' => 'V6 3.5L EcoBoost', 'color' => 'Rouge',
                'doors' => 4, 'seats' => 5, 'body_style' => 'Pickup', 'is_featured' => false,
                'features' => ['Climatisation', 'Caméra de recul', 'Jantes alliage', 'Bluetooth'],
                'description' => "Ford F-150 Raptor puissant, parfait pour usage tout-terrain.",
            ],
            [
                'brand' => 'Lexus', 'model' => 'RX 350', 'version' => 'F Sport AWD',
                'year' => 2021, 'condition' => 'occasion', 'status' => 'disponible',
                'price_usd' => 34200, 'mileage' => 27600, 'fuel_type' => 'Essence',
                'transmission' => 'Automatique', 'engine' => 'V6 3.5L', 'color' => 'Bleu',
                'doors' => 5, 'seats' => 5, 'body_style' => 'SUV', 'is_featured' => false,
                'features' => ['Climatisation', 'GPS', 'Sièges cuir', 'Vitres électriques'],
                'description' => "Lexus RX 350 F Sport, confort et fiabilité, très bien entretenue.",
            ],
            [
                'brand' => 'Chevrolet', 'model' => 'Tahoe', 'version' => 'RST 4WD',
                'year' => 2022, 'condition' => 'occasion', 'status' => 'disponible',
                'price_usd' => 46800, 'mileage' => 19200, 'fuel_type' => 'Essence',
                'transmission' => 'Automatique', 'engine' => 'V8 5.3L', 'color' => 'Noir',
                'doors' => 5, 'seats' => 7, 'body_style' => 'SUV', 'is_featured' => true,
                'features' => ['Climatisation', 'Caméra de recul', 'GPS', 'Sièges chauffants', 'Système audio premium'],
                'description' => "Chevrolet Tahoe RST spacieux, idéal pour les grandes familles.",
            ],
        ];

        foreach ($vehicles as $index => $data) {
            $brand = Brand::where('name', $data['brand'])->first();

            $vehicle = Vehicle::create([
                'brand_id' => $brand->id,
                'model' => $data['model'],
                'version' => $data['version'],
                'year' => $data['year'],
                'condition' => $data['condition'],
                'status' => $data['status'],
                'price_usd' => $data['price_usd'],
                'price_fcfa' => $data['price_usd'] * self::USD_TO_FCFA,
                'deposit_percentage_snapshot' => $brand->deposit_percentage,
                'mileage' => $data['mileage'],
                'fuel_type' => $data['fuel_type'],
                'transmission' => $data['transmission'],
                'engine' => $data['engine'],
                'color' => $data['color'],
                'doors' => $data['doors'],
                'seats' => $data['seats'],
                'body_style' => $data['body_style'],
                'is_featured' => $data['is_featured'],
                'sold_at' => $data['status'] === 'vendu' ? now() : null,
            ]);

            $vehicle->translations()->create([
                'locale' => 'fr',
                'description' => $data['description'],
            ]);

            $featureIds = Feature::whereIn('name', $data['features'])->pluck('id');
            $vehicle->features()->sync($featureIds);

            $vehicle->photos()->create([
                'path' => "https://picsum.photos/seed/vehicle-{$index}/800/600",
                'position' => 0,
            ]);
        }
    }
}
