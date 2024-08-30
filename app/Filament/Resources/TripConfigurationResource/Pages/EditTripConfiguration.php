<?php

namespace App\Filament\Resources\TripConfigurationResource\Pages;

use App\Filament\Resources\TripConfigurationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTripConfiguration extends EditRecord
{
    protected static string $resource = TripConfigurationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
