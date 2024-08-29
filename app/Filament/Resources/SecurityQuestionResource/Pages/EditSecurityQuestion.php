<?php

namespace App\Filament\Resources\SecurityQuestionResource\Pages;

use App\Filament\Resources\SecurityQuestionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSecurityQuestion extends EditRecord
{
    protected static string $resource = SecurityQuestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
