<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class VinDecoderService
{
    public function decode(string $vin): ?array
    {
        $response = Http::timeout(10)->get("https://vpic.nhtsa.dot.gov/api/vehicles/DecodeVinValues/{$vin}", [
            'format' => 'json',
        ]);

        if (! $response->ok()) {
            return null;
        }

        $result = $response->json('Results.0');

        if (! $result || empty($result['Make'])) {
            return null;
        }

        return [
            'make' => $result['Make'] ?? null,
            'model' => $result['Model'] ?? null,
            'version' => $result['Trim'] ?? null,
            'year' => $result['ModelYear'] ?? null,
            'body_style' => $result['BodyClass'] ?? null,
            'fuel_type' => $result['FuelTypePrimary'] ?? null,
            'transmission' => $result['TransmissionStyle'] ?? null,
            'engine' => trim(($result['EngineCylinders'] ?? '').'  '.($result['DisplacementL'] ?? '').'L '.($result['EngineConfiguration'] ?? '')) ?: null,
            'doors' => $result['Doors'] ?? null,
        ];
    }
}
