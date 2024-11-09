<?php

namespace App\Filament\Resources\Budgeting\IncomeResource\Pages;

use App\Filament\Resources\Budgeting\IncomeResource;
use App\Models\Income;
use Filament\Resources\Pages\ViewRecord;

class ViewIncome extends ViewRecord
{
    protected static string $resource = IncomeResource::class;

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        /** @var Income $record */
        $record = $this->getRecord();

        return __('filament-panels::pages/list.income.view_budgeting').' '.$record->name;
    }
}
