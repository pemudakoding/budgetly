<?php

namespace App\Filament\Clusters\FinancialSetup\Resources\ExpenseResource\Pages;

use App\Enums\ExpenseCategory;
use App\Filament\Clusters\FinancialSetup\Resources\ExpenseResource;
use App\Filament\Clusters\FinancialSetup\Resources\ExpenseResource\Actions\ManageAccountAction;
use App\Models\Builders\ExpenseBuilder;
use Filament\Actions;
use Filament\Actions\Contracts\HasActions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

class ManageExpenses extends ManageRecords
{
    protected static string $resource = ExpenseResource::class;

    public function getSubheading(): string|Htmlable|null
    {
        return new HtmlString(
            '<span class="text-base"> List of your expenses to start tracking and managing your spending—take control of your finances! </span>'
        );
    }

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
            ManageAccountAction::make(),
            Actions\CreateAction::make()
                ->using(function (array $data, HasActions $livewire, Actions\CreateAction $action): Model {
                    $data = [
                        ...$data,
                        'user_id' => auth()->user()->id,
                    ];

                    if ($translatableContentDriver = $livewire->makeFilamentTranslatableContentDriver()) {
                        $record = $translatableContentDriver->makeRecord($this->getModel(), $data);
                    } else {
                        $record = new ($this->getModel());
                        $record->fill($data);
                    }

                    if ($relationship = $action->getRelationship()) {
                        /** @phpstan-ignore-next-line */
                        $relationship->save($record);

                        return $record;
                    }

                    $record->save();

                    return $record;
                }),

        ];
    }
}
