<?php

namespace App\Filament\Resources\BadgesResource\Pages;

use App\Filament\Resources\BadgesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBadges extends EditRecord
{
    protected static string $resource = BadgesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
