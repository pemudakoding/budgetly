<?php

namespace App\Filament\Resources\Budgeting\ExpenseResource\Actions;

use App\Filament\Forms\MoneyInput;
use App\Models\Expense;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\CreateAction;

class QuickExpenseAction extends CreateAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('New Expense')
            ->icon('heroicon-s-plus')
            ->form([
                TextInput::make('description')
                    ->required()
                    ->maxLength(255),
                MoneyInput::make('amount')
                    ->required(),
            ])
            ->modalHeading(fn (Expense $record): string => 'Create expense: '.$record->name)
            ->action(function (array $data, Expense $record): void {
                $data['expense_id'] = $record->id;

                $record->budgets()->create($data);

                $this->success();
            });
    }

    public function canCreateAnother(): bool
    {
        return true;
    }
}
