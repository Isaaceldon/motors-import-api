<?php

namespace App\Filament\Resources\SpareParts\Pages;

use App\Filament\Resources\SpareParts\SparePartResource;
use App\Models\User;
use App\Notifications\NewSparePartArrivalNotification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Notification;

class CreateSparePart extends CreateRecord
{
    protected static string $resource = SparePartResource::class;

    protected function afterCreate(): void
    {
        $paths = $this->data['photo_uploads'] ?? [];

        foreach ($paths as $index => $path) {
            $this->record->photos()->create([
                'path' => $path,
                'position' => $index,
            ]);
        }

        if ($this->record->online_sale_enabled && $this->record->stock_quantity > 0) {
            $this->record->load('photos');

            Notification::send(
                User::where('role', 'client')->get(),
                new NewSparePartArrivalNotification($this->record)
            );
        }
    }
}
