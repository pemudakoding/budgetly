<?php

namespace App\Filament\Resources\Budgeting\AccountResource\Pages;

use App\Filament\Resources\Budgeting\AccountResource;
use App\Models\Account;
use Filament\Forms\Form;
use Filament\Resources\Pages\ViewRecord;

class ViewAccount extends ViewRecord
{
    protected static string $resource = AccountResource::class;

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        /** @var Account $record */
        $record = $this->getRecord();

        return __('budgetly::pages/transfer.detail', ['account' => $record->name]);
    }

    /**
     * @return array<int | string, string | Form>
     */
    protected function getForms(): array
    {
        return [
            'form' => $this->makeForm(),
        ];
    }
}
