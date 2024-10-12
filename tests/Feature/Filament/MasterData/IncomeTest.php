<?php

use App\Filament\Clusters\MasterData\Resources\IncomeResource;
use App\Models\Account;
use App\Models\Income;
use App\Models\User;
use Filament\Actions\CreateAction;

use function Pest\Laravel\get;
use function Pest\Livewire\livewire;
use function Tests\filamentActingAs;

test('able to render the page', function () {
    filamentActingAs();

    get(IncomeResource::getUrl())->assertOk();
})->group('feature', 'master-data', 'income');

test('able to get user incomes', function () {
    $user = User::factory()
        ->has(
            Income::factory(5)
                ->for(Account::factory()->for(User::factory())),
            'incomes'
        )->create();

    filamentActingAs($user);

    livewire(\App\Filament\Clusters\MasterData\Resources\IncomeResource\Pages\ManageIncomes::class)
        ->assertCanSeeTableRecords($user->incomes);
})->group('feature', 'master-data', 'income');

test('cannot see other user\'s incomes', function () {
    $user = User::factory()
        ->has(
            Income::factory(5)
                ->for(Account::factory()->for(User::factory())),
            'incomes'
        )->create();

    filamentActingAs();

    livewire(\App\Filament\Clusters\MasterData\Resources\IncomeResource\Pages\ManageIncomes::class)
        ->assertCanNotSeeTableRecords($user->incomes);
})->group('feature', 'master-data', 'income');

test('incomes created by the current user that hit the action', function () {
    $user = User::factory()
        ->has(
            Income::factory(1)
                ->for(Account::factory()->for(User::factory())),
            'incomes'
        )->create();

    filamentActingAs($user);

    expect($user->incomes->count())->toBe(1);

    livewire(\App\Filament\Clusters\MasterData\Resources\IncomeResource\Pages\ManageIncomes::class)
        ->callAction(
            CreateAction::getDefaultName(),
            [
                'name' => fake()->firstName(),
                'account_id' => Account::factory()->for($user)->create()->id,
            ],
        )
        ->assertHasNoActionErrors();

    expect($user->incomes()->count())->toBeGreaterThan(1);
})->group('feature', 'master-data', 'income');
