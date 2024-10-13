<?php

namespace App\Filament\Resources\Budgeting\ExpenseResource\Pages;

use App\Enums\ExpenseCategory;
use App\Filament\Resources\Budgeting\ExpenseResource;
use App\Models\Builders\ExpenseBuilder;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

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
            Actions\CreateAction::make(),
        ];
    }
}
