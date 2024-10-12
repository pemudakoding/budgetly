<?php

use App\Filament\Resources\MasterData\IncomeResource;
use App\Models\Income;
use App\Models\User;
use Filament\Actions\CreateAction;

use function Pest\Laravel\get;
use function Pest\Livewire\livewire;
use function Tests\filamentActingAs;

test('able to render the page', function () {
    filamentActingAs();

    get(IncomeResource::getUrl())->assertOk();
})->group('feature', 'setup', 'income');

test('able to get user incomes', function () {
    $user = User::factory()
        ->has(
            Income::factory(5),
            'incomes'
        )->create();

    filamentActingAs($user);

    livewire(IncomeResource\Pages\ManageIncomes::class)
        ->assertCanSeeTableRecords($user->incomes);
})->group('feature', 'setup', 'income');

test('cannot see other user\'s incomes', function () {
    $user = User::factory()
        ->has(
            Income::factory(5),
            'incomes'
        )->create();

    filamentActingAs();

    livewire(IncomeResource\Pages\ManageIncomes::class)
        ->assertCanNotSeeTableRecords($user->incomes);
})->group('feature', 'setup', 'income');

test('incomes created by the current user that hit the action', function () {
    $user = User::factory()
        ->has(
            Income::factory(1),
            'incomes'
        )->create();

    filamentActingAs($user);

    expect($user->incomes->count())->toBe(1);

    livewire(IncomeResource\Pages\ManageIncomes::class)
        ->callAction(
            CreateAction::getDefaultName(),
            [
                'name' => fake()->firstName(),
            ],
        )
        ->assertHasNoActionErrors();

    expect($user->incomes()->count())->toBeGreaterThan(1);
})->group('feature', 'setup', 'income');
