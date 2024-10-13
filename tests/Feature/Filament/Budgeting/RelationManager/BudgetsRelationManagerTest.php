<?php

use App\Enums\Month;
use App\Filament\Resources\Budgeting\IncomeResource\Pages\ViewIncome;
use App\Filament\Resources\Budgeting\IncomeResource\RelationManagers\BudgetsRelationManager;
use App\Models\Account;
use App\Models\Income;
use App\Models\IncomeBudget;
use Filament\Tables\Actions\CreateAction;
use Illuminate\Support\Carbon;

use function Pest\Laravel\travelBack;
use function Pest\Laravel\travelTo;
use function Pest\Livewire\livewire;
use function Tests\filamentActingAs;

test('able to list incomes', function () {
    $user = filamentActingAs();

    $income = Income::factory()
        ->has(IncomeBudget::factory(5), 'budgets')
        ->for($user)
        ->for(Account::factory()->for($user))
        ->create();

    livewire(BudgetsRelationManager::class, [
        'ownerRecord' => $income,
        'pageClass' => ViewIncome::class,
    ])
        ->assertCanSeeTableRecords($income->budgets);
})
    ->group('feature', 'filament', 'budgeting', 'relation-manager');

test('able create new income budget if not exists for the selected month', function () {
    $user = filamentActingAs();

    $income = Income::factory()
        ->for($user)
        ->for(Account::factory()->for($user))
        ->create();

    livewire(BudgetsRelationManager::class, [
        'ownerRecord' => $income,
        'pageClass' => ViewIncome::class,
    ])
        ->callTableAction(
            CreateAction::getDefaultName(),
            data: [
                'amount' => 1000,
                'month' => Month::January->value,
            ]
        )
        ->assertHasNoActionErrors();

    expect($income->refresh()->budgets()->count())->toBe(1);
})
    ->group('feature', 'filament', 'budgeting', 'relation-manager');

test('will not able to create a budget if have the same month in the same year', function () {
    $user = filamentActingAs();

    $income = Income::factory()
        ->for($user)
        ->for(Account::factory()->for($user))
        ->create();

    livewire(BudgetsRelationManager::class, [
        'ownerRecord' => $income,
        'pageClass' => ViewIncome::class,
    ])
        ->callTableAction(
            CreateAction::getDefaultName(),
            data: [
                'amount' => 1000,
                'month' => Month::January->value,
            ]
        )
        ->callTableAction(
            CreateAction::getDefaultName(),
            data: [
                'amount' => 5000,
                'month' => Month::January->value,
            ]
        )
        ->assertHasErrors(['mountedTableActionsData.0.month']);

    expect($income->refresh()->budgets()->count())->toBe(1);
})
    ->group('feature', 'filament', 'budgeting', 'relation-manager');

test('will  able to create a budget if have the same month in the different year', function () {
    $user = filamentActingAs();

    $income = Income::factory()
        ->for($user)
        ->for(Account::factory()->for($user))
        ->create();

    travelTo(Carbon::now()->subYear());

    livewire(BudgetsRelationManager::class, [
        'ownerRecord' => $income,
        'pageClass' => ViewIncome::class,
    ])
        ->callTableAction(
            CreateAction::getDefaultName(),
            data: [
                'amount' => 1000,
                'month' => Month::January->value,
            ]
        )
        ->assertHasNoActionErrors();

    expect($income->refresh()->budgets()->count())->toBe(1);

    travelBack(Carbon::now()->subYear());

    livewire(BudgetsRelationManager::class, [
        'ownerRecord' => $income,
        'pageClass' => ViewIncome::class,
    ])
        ->callTableAction(
            CreateAction::getDefaultName(),
            data: [
                'amount' => 2000,
                'month' => Month::January->value,
            ]
        )
        ->assertHasNoActionErrors();

    expect($income->refresh()->budgets()->count())->toBe(2);
})
    ->group('feature', 'filament', 'budgeting', 'relation-manager');
