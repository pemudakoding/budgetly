<?php

use App\Filament\Clusters\MasterData\Resources\ExpenseResource;
use App\Models\Account;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\ExpenseCategoryAccount;
use App\Models\User;
use Filament\Actions\CreateAction;

use function Pest\Laravel\get;
use function Pest\Livewire\livewire;
use function Tests\filamentActingAs;

test('able to render the page', function () {
    filamentActingAs();

    get(ExpenseResource::getUrl())->assertOk();
})->group('feature', 'master-data', 'expense');

test('able to get user expenses', function () {
    $user = User::factory()
        ->has(
            Expense::factory(5)->for(ExpenseCategory::factory(), 'category'),
            'expenses'
        )->create();

    filamentActingAs($user);

    livewire(\App\Filament\Clusters\MasterData\Resources\ExpenseResource\Pages\ManageExpenses::class)
        ->assertCanSeeTableRecords($user->expenses);
})->group('feature', 'master-data', 'expense');

test('cannot see other user\'s expenses', function () {
    $user = User::factory()
        ->has(
            Expense::factory(5)->for(ExpenseCategory::factory(), 'category'),
            'expenses'
        )->create();

    filamentActingAs();

    livewire(\App\Filament\Clusters\MasterData\Resources\ExpenseResource\Pages\ManageExpenses::class)
        ->assertCanNotSeeTableRecords($user->expenses);
})->group('feature', 'master-data', 'expense');

test('expenses created by the current user that hit the action', function () {
    $user = User::factory()
        ->has(
            Expense::factory(1)->for(ExpenseCategory::factory(), 'category'),
            'expenses'
        )->create();

    filamentActingAs($user);

    expect($user->expenses->count())->toBe(1);

    livewire(\App\Filament\Clusters\MasterData\Resources\ExpenseResource\Pages\ManageExpenses::class)
        ->callAction(
            CreateAction::getDefaultName(),
            [
                'name' => fake()->hexColor(),
                'expense_category_id' => ExpenseCategory::factory()->create()->id,
            ],
        )
        ->assertHasNoActionErrors();

    expect($user->expenses()->count())->toBeGreaterThan(1);
})->group('feature', 'master-data', 'expense');

test('able to set account for expense categories', function () {
    $user = User::factory()
        ->has(
            Expense::factory(1)->for(ExpenseCategory::factory(), 'category'),
            'expenses'
        )
        ->has(Account::factory())
        ->create();

    filamentActingAs($user);

    livewire(\App\Filament\Clusters\MasterData\Resources\ExpenseResource\Pages\ManageExpenses::class)
        ->callAction(
            \App\Filament\Clusters\MasterData\Resources\ExpenseResource\Actions\ManageAccountAction::getDefaultName(),
            [
                ExpenseCategory::factory()->create()->name => $user->accounts->first()->id,
            ],
        )
        ->assertHasNoActionErrors();

    expect(ExpenseCategoryAccount::where('user_id', $user->id)->count())->toBe(1);
})->group('feature', 'master-data', 'expense');

test('able to update account for expense categories if already settled previously', function () {
    $user = User::factory()
        ->has(
            Expense::factory(1)->for(ExpenseCategory::factory(), 'category'),
            'expenses'
        )
        ->has(Account::factory())
        ->create();

    filamentActingAs($user);

    livewire(\App\Filament\Clusters\MasterData\Resources\ExpenseResource\Pages\ManageExpenses::class)
        ->callAction(
            \App\Filament\Clusters\MasterData\Resources\ExpenseResource\Actions\ManageAccountAction::getDefaultName(),
            [
                ExpenseCategory::factory()->create()->name => $user->accounts->first()->id,
            ],
        )
        ->assertHasNoActionErrors();

    expect(ExpenseCategoryAccount::where('user_id', $user->id)->count())->toBe(1);

    livewire(\App\Filament\Clusters\MasterData\Resources\ExpenseResource\Pages\ManageExpenses::class)
        ->callAction(
            \App\Filament\Clusters\MasterData\Resources\ExpenseResource\Actions\ManageAccountAction::getDefaultName(),
            [
                ExpenseCategory::factory()->create()->name => $user->accounts->first()->id,
            ],
        )
        ->assertHasNoActionErrors();

    expect(ExpenseCategoryAccount::where('user_id', $user->id)->count())->toBe(1);
})->group('feature', 'master-data', 'expense');
