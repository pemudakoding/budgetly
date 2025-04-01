<?php

namespace App\Filament\Resources\Budgeting\AccountResource\Pages;

use App\Filament\Resources\Budgeting\AccountResource;
use Filament\Forms\Form;
use Filament\Resources\Pages\ViewRecord;

class ViewExpense extends ViewRecord
{
    protected static string $resource = AccountResource::class;

    /**
     * @return array<int | string, string | Form>
     */
    protected function getForms(): array
    {
        return [
            'form' => $this->makeForm(),
        ];
    }
}
