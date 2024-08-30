<?php

namespace App\Filament\Resources\TripConfigurationResource\Pages;

use App\Filament\Resources\TripConfigurationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTripConfigurations extends ListRecords
{
    protected static string $resource = TripConfigurationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
