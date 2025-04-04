<?php

namespace App\Livewire;

use App\Filament\Clusters\FinancialSetup\Resources\AccountResource;
use App\Filament\Pages\Dashboard;
use App\Models\ExpenseCategory;
use CodeWithDennis\SimpleAlert\Components\Infolists\SimpleAlert;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

/**
 * @property-read Form $form
 */
class OnboardIndex extends Page implements HasForms, HasInfolists
{
    use InteractsWithFormActions;
    use InteractsWithInfolists;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    protected static string $view = 'livewire.onboard-index';

    protected ?string $heading = '';

    protected static ?string $title = 'Onboard';

    public function mount(): void
    {
        $this->form->fill();
    }

    public static function canAccess(): bool
    {
        return ! Auth::user()->hasSetupFinancial();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make(__('budgetly::pages/onboard.wizard.preparing_your_account.title'))
                        ->icon('heroicon-o-credit-card')
                        ->schema([
                            \CodeWithDennis\SimpleAlert\Components\Forms\SimpleAlert::make('alert')
                                ->description(__('budgetly::pages/onboard.wizard.preparing_your_account.description')),
                            Repeater::make('accounts')
                                ->schema(AccountResource::form($form)->getComponents())
                                ->columns()
                                ->live()
                                ->label(__('budgetly::pages/onboard.wizard.preparing_your_account.accounts')),
                        ]),
                    Wizard\Step::make(__('budgetly::pages/onboard.wizard.income.title'))
                        ->icon('heroicon-o-banknotes')
                        ->schema([
                            \CodeWithDennis\SimpleAlert\Components\Forms\SimpleAlert::make('alert')
                                ->description(__('budgetly::pages/onboard.wizard.income.description')),
                            Repeater::make('incomes')
                                ->label(__('budgetly::pages/onboard.wizard.income.incomes'))
                                ->schema([
                                    TextInput::make('name')
                                        ->label(__('filament-forms::components.text_input.label.income.name'))
                                        ->helperText(__('budgetly::pages/onboard.wizard.income.helper.name')),
                                    Select::make('account')
                                        ->label(__('filament-forms::components.text_input.label.income.account'))
                                        ->hintIcon('heroicon-o-question-mark-circle')
                                        ->hintIconTooltip(__('budgetly::pages/onboard.wizard.income.hint.account'))
                                        ->required()
                                        ->options(function (Get $get) {
                                            $accountsState = array_filter($get('../../accounts'),
                                                fn (array $accountState) => $accountState['name'] !== null);

                                            return array_column($accountsState, 'name', 'name');
                                        }),
                                ])
                                ->columns(),
                        ]),
                    Wizard\Step::make(__('budgetly::pages/onboard.wizard.expense.title'))
                        ->icon('heroicon-o-clipboard-document-list')
                        ->schema([
                            \CodeWithDennis\SimpleAlert\Components\Forms\SimpleAlert::make('alert')
                                ->description(__('budgetly::pages/onboard.wizard.expense.description')),
                            Repeater::make('expenses')
                                ->label(__('budgetly::pages/onboard.wizard.expense.expenses'))
                                ->columns()
                                ->schema([
                                    TextInput::make('name')
                                        ->label(__('filament-forms::components.text_input.label.expense.name'))
                                        ->helperText(__('budgetly::pages/onboard.wizard.expense.helper.name'))
                                        ->autocomplete(false)
                                        ->required()
                                        ->string(),
                                    Select::make('expense_category_id')
                                        ->required()
                                        ->label(__('filament-forms::components.text_input.label.expense.category'))
                                        ->hintIcon('heroicon-o-question-mark-circle')
                                        ->hintIconTooltip(__('budgetly::pages/onboard.wizard.expense.hint.category'))
                                        ->options(array_map(fn (string $expense,
                                        ) => __('budgetly::expense-category.'.str($expense)->lower()),
                                            ExpenseCategory::pluck('name', 'id')->toArray()))
                                        ->exists(ExpenseCategory::class, column: 'id'),
                                    Select::make('account')
                                        ->label(__('filament-forms::components.text_input.label.income.account'))
                                        ->hintIcon('heroicon-o-question-mark-circle')
                                        ->hintIconTooltip(__('budgetly::pages/onboard.wizard.income.hint.account'))
                                        ->required()
                                        ->options(function (Get $get) {
                                            $accountsState = array_filter($get('../../accounts'),
                                                fn (array $accountState) => $accountState['name'] !== null);

                                            return array_column($accountsState, 'name', 'name');
                                        }),
                                ]),
                        ]),
                ])
                    ->submitAction(
                        Action::make('submit')
                            ->label('Submit')
                            ->submit('submit'),
                    ),
            ])
            ->model(Auth::user())
            ->statePath('data');
    }

    public function submit(): void
    {
        $user = Auth::user();
        [
            'accounts' => $accounts,
            'incomes' => $incomes,
            'expenses' => $expenses,
        ] = $this->form->getState();

        $addDateTime = function (array $item) use ($user): array {
            $item['created_at'] = now();
            $item['updated_at'] = now();
            $item['user_id'] = $user->id;

            return $item;
        };

        $accounts = array_map($addDateTime, $accounts);
        $expenses = array_map($addDateTime, $expenses);
        $incomes = array_map($addDateTime, $incomes);

        $user->accounts()->upsert($accounts, ['name', 'user_id']);

        $accounts = $user->refresh()->accounts->pluck('id', 'name');

        $setAccountId = function (array $item) use ($accounts): array {
            $item['account_id'] = $accounts[$item['account']];

            unset($item['account']);

            return $item;
        };

        $expenses = array_map($setAccountId, $expenses);

        $incomes = array_map($setAccountId, $incomes);

        $user->expenses()->upsert($expenses, ['name', 'user_id', 'account_id']);
        $user->incomes()->upsert($incomes, ['name', 'user_id', 'account_id']);

        Notification::make()
            ->title(__('filament-notifications::common.success'))
            ->body(__('filament-notifications::financial-setup.success_onboarding_financial_setup'))
            ->success()
            ->send();

        $this->redirect(Dashboard::getUrl());
    }

    protected function makeInfolist(): Infolist
    {
        return Infolist::make()
            ->schema([
                SimpleAlert::make('example')
                    ->title(__('filament-panels::pages/dashboard.alert.onboard-simple.title'))
                    ->description(__('filament-panels::pages/dashboard.alert.onboard-simple.description'))
                    ->info()
                    ->border()
                    ->columnSpanFull()
                    ->visible(! auth()->user()->hasSetupFinancial()),
            ]);
    }
}
