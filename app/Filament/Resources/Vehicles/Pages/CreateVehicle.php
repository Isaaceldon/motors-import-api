<?php

namespace App\Filament\Resources\Vehicles\Pages;

use App\Filament\Resources\Vehicles\VehicleResource;
use App\Models\User;
use App\Notifications\NewVehicleArrivalNotification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Notification;

class CreateVehicle extends CreateRecord
{
    protected static string $resource = VehicleResource::class;

    protected function afterCreate(): void
    {
        $paths = $this->data['photo_uploads'] ?? [];

        foreach ($paths as $index => $path) {
            $this->record->photos()->create([
                'path' => $path,
                'position' => $index,
            ]);
        }

        if ($this->record->status === 'disponible') {
            $this->record->load('photos', 'brand');

            Notification::send(
                User::where('role', 'client')->get(),
                new NewVehicleArrivalNotification($this->record)
            );
        }
    }
}
