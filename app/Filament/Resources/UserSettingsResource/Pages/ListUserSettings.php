<?php

namespace App\Filament\Resources\UserSettingsResource\Pages;

use App\Filament\Resources\UserSettingsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserSettings extends ListRecords
{
    protected static string $resource = UserSettingsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
