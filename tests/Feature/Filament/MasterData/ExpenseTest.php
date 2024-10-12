<?php

use App\Enums\ExpenseCategory;
use App\Filament\Resources\MasterData\ExpenseResource;
use App\Models\Expense;
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
            Expense::factory(5),
            'expenses'
        )->create();

    filamentActingAs($user);

    livewire(ExpenseResource\Pages\ManageExpenses::class)
        ->assertCanSeeTableRecords($user->expenses);
})->group('feature', 'master-data', 'expense');

test('cannot see other user\'s expenses', function () {
    $user = User::factory()
        ->has(
            Expense::factory(5),
            'expenses'
        )->create();

    filamentActingAs();

    livewire(ExpenseResource\Pages\ManageExpenses::class)
        ->assertCanNotSeeTableRecords($user->expenses);
})->group('feature', 'master-data', 'expense');

test('expenses created by the current user that hit the action', function () {
    $user = User::factory()
        ->has(
            Expense::factory(1),
            'expenses'
        )->create();

    filamentActingAs($user);

    expect($user->expenses->count())->toBe(1);

    livewire(ExpenseResource\Pages\ManageExpenses::class)
        ->callAction(
            CreateAction::getDefaultName(),
            [
                'name' => fake()->hexColor(),
                'category' => ExpenseCategory::Wants->value,
            ],
        )
        ->assertHasNoActionErrors();

    expect($user->expenses()->count())->toBeGreaterThan(1);
})->group('feature', 'master-data', 'expense');
