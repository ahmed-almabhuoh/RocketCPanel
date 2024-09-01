<?php

namespace App\Filament\Resources\VehicleScheduleResource\Pages;

use App\Filament\Resources\VehicleScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVehicleSchedule extends EditRecord
{
    protected static string $resource = VehicleScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
