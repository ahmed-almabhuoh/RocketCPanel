<?php

namespace App\Filament\Resources\TransferLogResource\Pages;

use App\Filament\Resources\TransferLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransferLogs extends ListRecords
{
    protected static string $resource = TransferLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
