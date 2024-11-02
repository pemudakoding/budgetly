<?php

namespace App\Filament\Resources\Budgeting\ExpenseResource\Actions;

use App\Filament\Forms\MoneyInput;
use App\Models\Expense;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
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
                DatePicker::make('realized_at')
                    ->label('Realized at')
                    ->default(Carbon::now()),
            ])
            ->modalHeading(fn (?Expense $record): string => 'Create expense: '.$record?->name)
            ->action(function (array $data, Expense $record, QuickExpenseAction $action, Form $form, array $arguments): void {
                $data['expense_id'] = $record->id;

                $record->budgets()->create($data);

                if ($arguments['another'] ?? false) {
                    $this->callAfter();
                    $this->sendSuccessNotification();

                    $form->fill();

                    $this->halt();

                    return;
                }

                $this->success();
            });
    }

    public function canCreateAnother(): bool
    {
        return true;
    }
}
