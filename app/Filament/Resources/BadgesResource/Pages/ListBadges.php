<?php

namespace App\Filament\Resources\BadgesResource\Pages;

use App\Filament\Resources\BadgesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBadges extends ListRecords
{
    protected static string $resource = BadgesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
