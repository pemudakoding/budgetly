<?php

namespace App\Filament\Resources\Budgeting\ExpenseResource\Pages;

use App\Enums\ExpenseCategory;
use App\Filament\Resources\Budgeting\ExpenseResource;
use App\Models\Builders\ExpenseBuilder;
use App\Models\Expense;
use App\Models\ExpenseBudget;
use App\ValueObjects\Money;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\RawJs;

class ListExpenses extends ListRecords
{
    protected static string $resource = ExpenseResource::class;

    public function getTabs(): array
    {

        return [
            'all' => Tab::make(),
            ...array_reduce(
                ExpenseCategory::cases(),
                function ($categories, ExpenseCategory $category): array {
                    $categories[lcfirst($category->value)] = Tab::make()->modifyQueryUsing(
                        fn (ExpenseBuilder $query): ExpenseBuilder => $query->whereCategory($category)
                    );

                    return $categories;
                },
                []
            ),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->form([
                    TextInput::make('description')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('amount')
                        ->required()
                        ->mask(RawJs::make('$money($input, \',\', \'.\')'))
                        ->prefix('Rp.')
                        ->dehydrateStateUsing(fn (?string $state) => Money::makeFromFilamentMask($state)->value),
                    Select::make('expense_id')
                        ->label('Expense category')
                        ->options(Expense::query()->pluck('name', 'id')->toArray())
                        ->required()
                        ->searchable(),
                ])
                ->using(function (array $data) {
                    ExpenseBudget::query()->create($data);
                }),
        ];
    }
}
