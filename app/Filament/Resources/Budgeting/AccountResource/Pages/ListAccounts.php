<?php

namespace App\Filament\Resources\Budgeting\AccountResource\Pages;

use App\Filament\Resources\Budgeting\AccountResource;
use Filament\Resources\Pages\ListRecords;

class ListAccounts extends ListRecords
{
    protected static string $resource = AccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
