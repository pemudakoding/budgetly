<?php

use App\Enums\Route as RouteEnum;
use App\Livewire\OnboardIndex;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Support\Facades\Route;

Route::middleware(\Illuminate\Support\Facades\Config::array('filament.middleware.common'))
    ->middleware('panel:'.Filament::getCurrentPanel()->getId())
    ->group(function () {
        Route::middleware(Authenticate::class)
            ->group(function () {
                Route::name(RouteEnum::Onboard)->get('/onboard', OnboardIndex::class);
            });
    });
