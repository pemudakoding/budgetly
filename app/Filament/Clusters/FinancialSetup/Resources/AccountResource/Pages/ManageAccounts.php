<?php

namespace App\Filament\Clusters\FinancialSetup\Resources\AccountResource\Pages;

use App\Filament\Clusters\FinancialSetup\Resources\AccountResource;
use Filament\Actions;
use Filament\Actions\Contracts\HasActions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

class ManageAccounts extends ManageRecords
{
    protected static string $resource = AccountResource::class;

    protected static ?string $navigationGroup = '';

    public function getSubheading(): string|Htmlable|null
    {
        return new HtmlString(
            '<span class="text-base"> '.__('filament-panels::pages/financial-setup.account.description').' </span>'
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
