<?php

namespace App\Filament\Resources\Setup\ExpenseResource\Pages;

use App\Enums\ExpenseCategory;
use App\Filament\Resources\Setup\ExpenseResource;
use App\Models\Builders\ExpenseBuilder;
use Filament\Actions;
use Filament\Actions\Contracts\HasActions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Model;

class ManageExpenses extends ManageRecords
{
    protected static string $resource = ExpenseResource::class;

    public function getTabs(): array
    {

        return [
            'all' => Tab::make(),
            ...array_reduce(
                ExpenseCategory::toArray(),
                function ($categories, $category): array {
                    $categories[lcfirst($category)] = Tab::make()->modifyQueryUsing(
                        fn (ExpenseBuilder $query): ExpenseBuilder => $query->where('category', $category)
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
                ->using(function (array $data, HasActions $livewire, Actions\CreateAction $action): Model {
                    if ($translatableContentDriver = $livewire->makeFilamentTranslatableContentDriver()) {
                        $record = $translatableContentDriver->makeRecord($this->getModel(), $data);
                    } else {
                        $record = new ($this->getModel());
                        $record->fill([
                            ...$data,
                            'user_id' => auth()->user()->id,
                        ]);
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
