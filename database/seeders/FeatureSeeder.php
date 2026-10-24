<?php

namespace Database\Seeders;

use App\Models\Feature;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FeatureSeeder extends Seeder
{
    public function run(): void
    {
        $features = [
            'Climatisation',
            'Caméra de recul',
            'GPS',
            'Sièges cuir',
            'Toit ouvrant',
            'Régulateur de vitesse',
            'Bluetooth',
            'Vitres électriques',
            'Système audio premium',
            'Aide au stationnement',
            'Sièges chauffants',
            'Jantes alliage',
        ];

        foreach ($features as $name) {
            Feature::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );
        }
    }
}
