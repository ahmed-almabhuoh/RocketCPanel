<?php

namespace App\Filament\Resources\DriverPositionResource\Pages;

use App\Filament\Resources\DriverPositionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDriverPositions extends ListRecords
{
    protected static string $resource = DriverPositionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
