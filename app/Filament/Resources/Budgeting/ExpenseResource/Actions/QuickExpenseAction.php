<?php

namespace App\Filament\Resources\Budgeting\ExpenseResource\Actions;

use App\Models\Expense;
use App\ValueObjects\Money;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Support\RawJs;
use Filament\Tables\Actions\CreateAction;

class QuickExpenseAction extends CreateAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('New')
            ->icon('heroicon-s-plus')
            ->form([
                TextInput::make('description')
                    ->required()
                    ->maxLength(255),
                TextInput::make('amount')
                    ->required()
                    ->mask(RawJs::make('$money($input, \',\', \'.\')'))
                    ->prefix('Rp.')
                    ->dehydrateStateUsing(fn (?string $state) => Money::makeFromFilamentMask($state)->value),
                Hidden::make('expense_id')
                    ->default(fn (Expense $record): int => $record->id),
            ])
            ->modalHeading(fn (Expense $record): string => 'Create expense: '.$record->name)
            ->action(function (array $data, Expense $record): void {
                $record->budgets()->create($data);

                $this->success();
            });
    }

    public function canCreateAnother(): bool
    {
        return true;
    }
}
