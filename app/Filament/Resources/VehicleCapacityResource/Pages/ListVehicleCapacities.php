<?php

namespace App\Filament\Resources\VehicleCapacityResource\Pages;

use App\Filament\Resources\VehicleCapacityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVehicleCapacities extends ListRecords
{
    protected static string $resource = VehicleCapacityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
