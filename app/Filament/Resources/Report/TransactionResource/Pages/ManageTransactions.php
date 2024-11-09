<?php

namespace App\Filament\Resources\Report\TransactionResource\Pages;

use App\Enums\ExpenseCategory;
use App\Filament\Resources\Report\TransactionResource;
use App\Models\Builders\ExpenseBudgetBuilder;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class ManageTransactions extends ManageRecords
{
    protected static string $resource = TransactionResource::class;

    public function getSubheading(): string|Htmlable|null
    {
        return new HtmlString(
            '<span class="text-base"> '.__('filament-panels::pages/list.transaction.description').' </span>'
        );
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(),
            ...array_reduce(
                ExpenseCategory::cases(),
                function ($categories, ExpenseCategory $category): array {
                    $categories[$category->render()] = Tab::make()
                        ->icon($category->resolveIcon())
                        ->modifyQueryUsing(
                            fn (ExpenseBudgetBuilder $query): ExpenseBudgetBuilder => $query->whereCategory($category)
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

        ];
    }
}
