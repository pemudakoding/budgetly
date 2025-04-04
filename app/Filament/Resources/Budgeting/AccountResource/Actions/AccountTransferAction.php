<?php

namespace App\Filament\Resources\Budgeting\AccountResource\Actions;

use App\Concerns\AccountBalanceCalculation;
use App\Filament\Forms\MoneyInput;
use App\Models\Account;
use App\Models\AccountTransfer;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Tables\Actions\CreateAction;

class AccountTransferAction extends CreateAction
{
    use AccountBalanceCalculation;

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Transfer')
            ->icon('heroicon-s-paper-airplane')
            ->model(AccountTransfer::class)
            ->label(__('budgetly::pages/transfer.title'))
            ->form([
                MoneyInput::make('amount')
                    ->required()
                    ->label(__('budgetly::pages/transfer.amount'))
                    ->hint(function (Account $record) {
                        return __('budgetly::pages/transfer.available_balance', [
                            'balance' => self::calculateRemainingBalance($record->id, true),
                        ]);
                    }),
                MoneyInput::make('fee')
                    ->default(0)
                    ->label(__('budgetly::pages/transfer.fee')),
                Select::make('to_account_id')
                    ->required()
                    ->live()
                    ->label(__('budgetly::pages/transfer.to_account'))
                    ->disableOptionWhen(fn (string $value, Account $record): bool => $value == $record->id)
                    ->options(Account::whereUserId(auth()->id())->pluck('name', 'id')->toArray()),
                TextInput::make('description')
                    ->label(__('budgetly::pages/transfer.description')),
                DateTimePicker::make('transfer_date')
                    ->required()
                    ->default(now())
                    ->label(__('budgetly::pages/transfer.transfer_date')),
            ])
            ->modalHeading(fn (?Account $record,
            ): string => __('budgetly::pages/transfer.transfer_account').$record?->name)
            ->action(function (
                array $data,
                Account $record,
                AccountTransferAction $action,
                Form $form,
                array $arguments,
            ): void {
                $data['from_account_id'] = $record->id;

                AccountTransfer::create($data);

                if ($arguments['another'] ?? false) {
                    $this->callAfter();
                    $this->sendSuccessNotification();

                    $form->fill();

                    $this->halt();

                    return;
                }

                $this->success();
            });
    }

    public function canCreateAnother(): bool
    {
        return true;
    }
}
