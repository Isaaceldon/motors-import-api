<?php

namespace App\Filament\Resources\Vehicles\Pages;

use App\Filament\Resources\Vehicles\VehicleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVehicle extends EditRecord
{
    protected static string $resource = VehicleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $paths = $this->data['photo_uploads'] ?? [];

        // Only manage photos uploaded through this form (local disk paths);
        // photos with external URLs (e.g. seeded data) are left untouched.
        $this->record->photos()->where('path', 'not like', 'http%')->delete();

        foreach ($paths as $index => $path) {
            $this->record->photos()->create([
                'path' => $path,
                'position' => $index,
            ]);
        }
    }
}
