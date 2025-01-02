<?php

namespace App\Filament\Resources\Budgeting\IncomeResource\Actions;

use App\Enums\Month;
use App\Filament\Forms\MoneyInput;
use App\Models\Income;
use App\Models\IncomeBudget;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
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
            ->label(__('budgetly::actions.income.add_budget.title'))
            ->icon('heroicon-s-plus')
            ->form([
                MoneyInput::make('amount')
                    ->label(__('filament-forms::components.text_input.label.money.name'))
                    ->visible(fn (Income $income) => ! $income->is_fluctuating)
                    ->required(),
                Select::make('month')
                    ->label(__('filament-forms::components.text_input.label.month.name'))
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
                Repeater::make('history')
                    ->visible(fn (Income $income) => $income->is_fluctuating)
                    ->collapsible()
                    ->label(__('filament-forms::components.repeater.label.income.history'))
                    ->schema([
                        TextInput::make('description')
                            ->label(__('filament-forms::components.text_input.label.description.name'))
                            ->required()
                            ->maxLength(255),
                        MoneyInput::make('amount')
                            ->label(__('filament-forms::components.text_input.label.money.name'))
                            ->required(),
                        DatePicker::make('revenue_at')
                            ->label(__('filament-forms::components.text_input.label.income.history_date'))
                            ->required()
                            ->maxDate(now()),
                    ]),
            ])
            ->modalHeading(fn (?Income $record): string => __('budgetly::actions.income.modal_heading.create').': '.$record?->name)
            ->action(function (array $data, Income $record, QuickBudgetAction $action, Form $form, array $arguments): void {
                $data['income_id'] = $record->id;

                $budget = $record->budgets()->createOrFirst($data);

                $record->is_fluctuating && $budget->histories()->createMany($data['history']);

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
