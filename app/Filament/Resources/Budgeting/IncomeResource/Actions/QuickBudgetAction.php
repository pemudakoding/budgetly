<?php

namespace App\Filament\Resources\Budgeting\IncomeResource\Actions;

use App\Enums\Month;
use App\Filament\Forms\MoneyInput;
use App\Models\Income;
use App\Models\IncomeBudget;
use Carbon\Carbon;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Tables\Actions\CreateAction;
use Illuminate\Database\Query\Builder;
use Illuminate\Validation\Rules\Unique;

class QuickBudgetAction extends CreateAction
{
    public static function getDefaultName(): ?string
    {
        return 'quick-budget';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Add Budget')
            ->icon('heroicon-s-plus')
            ->form([
                MoneyInput::make('amount')
                    ->required(),
                Select::make('month')
                    ->options(Month::toArray())
                    ->required()
                    ->unique(
                        IncomeBudget::class,
                        'month',
                        modifyRuleUsing: fn (Unique $rule): Unique => $rule->where(
                            fn (Builder $query) => $query
                                ->whereYear('created_at', Carbon::now()->year)
                                ->where('income_id', $this->getRecord()->getKey())
                        )
                    ),
            ])
            ->modalHeading(fn (?Income $record): string => 'Create Income: '.$record?->name)
            ->action(function (array $data, Income $record, QuickBudgetAction $action, Form $form, array $arguments): void {
                $data['income_id'] = $record->id;

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
