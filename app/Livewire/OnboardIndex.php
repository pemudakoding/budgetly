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
                    Wizard\Step::make('Preparing your account')
                        ->icon('heroicon-o-credit-card')
                        ->schema([
                            \CodeWithDennis\SimpleAlert\Components\Forms\SimpleAlert::make('alert')
                                ->description('Add your bank to start tracking your finances in one place.'),
                            Repeater::make('accounts')
                                ->schema(AccountResource::form($form)->getComponents())
                                ->columns()
                                ->live(),
                        ]),
                    Wizard\Step::make('Income')
                        ->icon('heroicon-o-banknotes')
                        ->schema([
                            \CodeWithDennis\SimpleAlert\Components\Forms\SimpleAlert::make('alert')
                                ->description('Add an income source to easily track your earnings and expenses! This helps you manage spending based on your current financial state.'),
                            Repeater::make('incomes')
                                ->schema([
                                    TextInput::make('name')
                                        ->helperText('Example: Monthly Salary or Freelance earnings'),
                                    Select::make('account')
                                        ->hintIcon('heroicon-o-question-mark-circle')
                                        ->hintIconTooltip('Where does the income come from?')
                                        ->required()
                                        ->options(function (Get $get) {
                                            $accountsState = array_filter($get('../../accounts'), fn (array $accountState) => $accountState['name'] !== null);

                                            return array_column($accountsState, 'name', 'name');
                                        })
                                        ->label('Account'),
                                ])
                                ->columns(),
                        ]),
                    Wizard\Step::make('Expense')
                        ->icon('heroicon-o-clipboard-document-list')
                        ->schema([
                            \CodeWithDennis\SimpleAlert\Components\Forms\SimpleAlert::make('alert')
                                ->description('Add your expenses to start tracking and managing your spending—take control of your finances!'),
                            Repeater::make('expenses')
                                ->columns()
                                ->schema([
                                    TextInput::make('name')
                                        ->helperText('Example: Home Rent or Transportation')
                                        ->autocomplete(false)
                                        ->required()
                                        ->string(),
                                    Select::make('expense_category_id')
                                        ->required()
                                        ->label('Expense Category')
                                        ->hintIcon('heroicon-o-question-mark-circle')
                                        ->hintIconTooltip('What category does this expense belong to?')
                                        ->options(ExpenseCategory::pluck('name', 'id'))
                                        ->exists(ExpenseCategory::class, column: 'id'),
                                ]),
                        ]),
                ])
                    ->submitAction(
                        Action::make('submit')
                            ->label('Submit')
                            ->submit('submit')
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
        $user->expenses()->upsert($expenses, ['name', 'user_id']);

        $accounts = $user->refresh()->accounts->pluck('id', 'name');

        $incomes = array_map(
            function (array $income) use ($accounts) {
                $income['account_id'] = $accounts[$income['account']];

                unset($income['account']);

                return $income;
            },
            $incomes
        );

        $user->incomes()->upsert($incomes, ['name', 'user_id']);

        Notification::make()
            ->title('Success')
            ->body('Great! Your financial setup is complete. You\'re all set to explore the features!')
            ->success()
            ->send();

        $this->redirect(Dashboard::getUrl());
    }

    protected function makeInfolist(): Infolist
    {
        return Infolist::make()
            ->schema([
                SimpleAlert::make('example')
                    ->title('Just One Step to Unlock!')
                    ->description('Complete your financial setup by adding your accounts, expenses, and income to get ready to explore our features!')
                    ->info()
                    ->border()
                    ->columnSpanFull()
                    ->visible(! auth()->user()->hasSetupFinancial()),
            ]);
    }
}
