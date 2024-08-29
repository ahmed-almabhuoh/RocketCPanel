<?php

namespace App\Filament\Resources\TransferLogResource\Pages;

use App\Filament\Resources\TransferLogResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTransferLog extends EditRecord
{
    protected static string $resource = TransferLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
