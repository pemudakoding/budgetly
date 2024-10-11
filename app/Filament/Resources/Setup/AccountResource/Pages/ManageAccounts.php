<?php

namespace App\Filament\Resources\Setup\AccountResource\Pages;

use App\Filament\Resources\Setup\AccountResource;
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
