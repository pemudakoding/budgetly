<?php

namespace App\Filament\Resources\Budgeting\ExpenseResource\Pages;

use App\Filament\Resources\Budgeting\ExpenseResource;
use App\Models\Income;
use Filament\Forms\Form;
use Filament\Resources\Pages\ViewRecord;

class ViewExpense extends ViewRecord
{
    protected static string $resource = ExpenseResource::class;

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        /** @var Income $record */
        $record = $this->getRecord();

        return __('filament-panels::pages/list.expense.view', ['name' => $record->name]);
    }

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
