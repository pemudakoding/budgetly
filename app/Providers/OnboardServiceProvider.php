<?php

namespace App\Providers;

use App\Enums\Role;
use App\Enums\Route;
use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Spatie\Onboard\Facades\Onboard;

class OnboardServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Onboard::addStep('Financial Setup')
            ->link(route(Route::Onboard, absolute: false))
            ->excludeIf(fn (User $user): bool => $user->hasRole(Role::Admin))
            ->completeIf(fn (User $model): bool => $model->accounts()->exists()
                && $model->incomes()->exists()
                && $model->expenses()->exists()
            );
    }
}
