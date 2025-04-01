<?php

namespace App\Filament\Resources\Budgeting\AccountTransferResource\Pages;

use App\Filament\Resources\Budgeting\AccountTransferResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAccountTransfers extends ListRecords
{
    protected static string $resource = AccountTransferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
