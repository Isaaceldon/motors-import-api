<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            ['name' => 'Toyota', 'deposit_percentage' => 10],
            ['name' => 'BMW', 'deposit_percentage' => 15],
            ['name' => 'Mercedes-Benz', 'deposit_percentage' => 15],
            ['name' => 'Ford', 'deposit_percentage' => 10],
            ['name' => 'Chevrolet', 'deposit_percentage' => 10],
            ['name' => 'Honda', 'deposit_percentage' => 8],
            ['name' => 'Lexus', 'deposit_percentage' => 12],
            ['name' => 'Audi', 'deposit_percentage' => 15],
        ];

        foreach ($brands as $brand) {
            Brand::updateOrCreate(
                ['slug' => Str::slug($brand['name'])],
                [
                    'name' => $brand['name'],
                    'deposit_percentage' => $brand['deposit_percentage'],
                ]
            );
        }
    }
}
