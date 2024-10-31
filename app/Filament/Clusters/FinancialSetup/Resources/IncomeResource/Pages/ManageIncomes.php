<?php

namespace App\Filament\Clusters\FinancialSetup\Resources\IncomeResource\Pages;

use App\Filament\Clusters\FinancialSetup\Resources\IncomeResource;
use Filament\Actions;
use Filament\Actions\Contracts\HasActions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

class ManageIncomes extends ManageRecords
{
    protected static string $resource = IncomeResource::class;

    public function getSubheading(): string|Htmlable|null
    {
        return new HtmlString(
            '<span class="text-base"> List of your income sources to start track and manage your earnings—optimize your financial growth! </span>'
        );
    }

    protected function getHeaderActions(): array
    {
        return [
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
