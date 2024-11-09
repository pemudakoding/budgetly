<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use App\Http\Middleware\RedirectToUnfinishedOnboardingStep;
use App\Livewire\Auth\Register;
use Exception;
use Filament\Http\Middleware\Authenticate;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentAsset;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Vite;
use Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin;

class AdminPanelProvider extends PanelProvider
{
    /**
     * @throws Exception
     */
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('user')
            ->brandLogo(asset('images/logo-light.svg'))
            ->darkModeBrandLogo(asset('images/logo-dark.svg'))
            ->brandLogoHeight('2.5rem')
            ->login()
            ->registration(Register::class)
            ->emailVerification()
            ->profile(isSimple: false)
            ->passwordReset()
            ->colors([
                'primary' => Color::Emerald,
                'secondary' => Color::Gray,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\\Filament\\Clusters')
            ->pages([
                Dashboard::class,
            ])
            ->plugins([
                FilamentApexChartsPlugin::make(),
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                //
            ])
            ->middleware(Config::array('filament.middleware.common'))
            ->authMiddleware([
                Authenticate::class,
                RedirectToUnfinishedOnboardingStep::class,
            ]);
    }

    public function boot(): void
    {
        Table::$defaultCurrency = 'idr';
        Table::$defaultNumberLocale = 'id';

        FilamentAsset::register([
            Css::make('app', Vite::asset('resources/css/app.css')),
            Js::make('app-js', Vite::asset('resources/js/app.js'))->module(! app()->isProduction()),
        ]);
    }
}
