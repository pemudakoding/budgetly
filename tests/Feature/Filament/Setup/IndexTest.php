<?php

use App\Filament\Resources\Setup\AccountResource;
use App\Models\Account;
use App\Models\User;
use Filament\Actions\CreateAction;

use function Pest\Laravel\get;
use function Pest\Livewire\livewire;
use function Tests\filamentActingAs;

test('able to render the page', function () {
    filamentActingAs();

    get(AccountResource::getUrl())->assertOk();
})->group('feature', 'setup', 'account');

test('able to get user accounts', function () {
    $user = User::factory()
        ->has(
            Account::factory(5),
            'accounts'
        )->create();

    filamentActingAs($user);

    livewire(AccountResource\Pages\ManageAccounts::class)
        ->assertCanSeeTableRecords($user->accounts);
})->group('feature', 'setup', 'account');

test('cannot see other user\'s accounts', function () {
    $user = User::factory()
        ->has(
            Account::factory(5),
            'accounts'
        )->create();

    filamentActingAs();

    livewire(AccountResource\Pages\ManageAccounts::class)
        ->assertCanNotSeeTableRecords($user->accounts);
})->group('feature', 'setup', 'account');

test('account create with current user that hit the action', function () {
    $user = User::factory()
        ->has(
            Account::factory(1),
            'accounts'
        )->create();

    filamentActingAs($user);

    expect($user->accounts->count())->toBe(1);

    livewire(AccountResource\Pages\ManageAccounts::class)
        ->callAction(
            CreateAction::getDefaultName(),
            [
                'legend' => fake()->hexColor(),
                'name' => fake()->firstName(),
            ],
        )
        ->assertHasNoActionErrors();

    expect($user->accounts()->count())->toBeGreaterThan(1);
})->group('feature', 'setup', 'account');
