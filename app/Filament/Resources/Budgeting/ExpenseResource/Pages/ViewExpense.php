<?php

namespace App\Filament\Resources\Budgeting\ExpenseResource\Pages;

use App\Filament\Resources\Budgeting\ExpenseResource;
use Filament\Resources\Pages\ViewRecord;

class ViewExpense extends ViewRecord
{
    protected static string $resource = ExpenseResource::class;

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        /** @var \App\Models\Expense $model */
        $model = $this->getRecord();

        return 'View Expenses of '.' '.$model->name;
    }
}
