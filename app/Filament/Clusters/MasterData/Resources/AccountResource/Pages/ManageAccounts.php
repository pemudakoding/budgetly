<?php

namespace App\Filament\Clusters\MasterData\Resources\AccountResource\Pages;

use App\Filament\Clusters\MasterData\Resources\AccountResource;
use Filament\Actions;
use Filament\Actions\Contracts\HasActions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Model;

class ManageAccounts extends ManageRecords
{
    protected static string $resource = AccountResource::class;

    protected static ?string $navigationGroup = '';

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
