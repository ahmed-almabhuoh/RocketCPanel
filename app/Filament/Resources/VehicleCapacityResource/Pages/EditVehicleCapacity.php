<?php

namespace App\Filament\Resources\VehicleCapacityResource\Pages;

use App\Filament\Resources\VehicleCapacityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVehicleCapacity extends EditRecord
{
    protected static string $resource = VehicleCapacityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
