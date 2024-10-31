<?php

namespace App\Livewire;

use App\Filament\Clusters\FinancialSetup\Resources\AccountResource;
use App\Models\ExpenseCategory;
use CodeWithDennis\SimpleAlert\Components\Infolists\SimpleAlert;
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

    public function mount(): void
    {
        $this->form->fill();
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
                                    Select::make('accounts')
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
                ]),
            ])
            ->model(Auth::user())
            ->statePath('data');
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
