<?php

use App\Filament\Resources\Budgeting\ExpenseResource;
use App\Filament\Resources\Budgeting\ExpenseResource\Actions\QuickExpenseAction;
use App\Filament\Resources\Budgeting\ExpenseResource\Pages\ListExpenses;
use App\Models\Expense;

use function Pest\Laravel\get;
use function Pest\Livewire\livewire;
use function Tests\filamentActingAs;

test('able to render the page', function () {
    filamentActingAs();

    get(ExpenseResource::getUrl())->assertOk();
})->group('feature', 'budgeting', 'expense');

test('able quick create new expense budget', function () {
    $user = filamentActingAs();

    $expense = Expense::factory()
        ->for($user)
        ->create();

    livewire(ListExpenses::class)
        ->callTableAction(
            name: QuickExpenseAction::class,
            record: $expense,
            data: [
                'description' => fake()->userName(),
                'amount' => fake()->numberBetween(10000, 100000),
            ],
        )
        ->assertHasNoActionErrors();

    expect($expense->refresh()->count())->toBe(1);
})->group('feature', 'budgeting', 'expense');
