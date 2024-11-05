<?php

namespace App\Filament\Pages;

use CodeWithDennis\SimpleAlert\Components\Infolists\SimpleAlert;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class Dashboard extends Page implements HasInfolists
{
    use InteractsWithInfolists;

    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.pages.dashboard';

    public function getTitle(): string|Htmlable
    {
        return __('filament-panels::pages/dashboard.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament-panels::pages/dashboard.title');
    }

    protected function makeInfolist(): Infolist
    {
        return Infolist::make()
            ->schema([
                SimpleAlert::make('example')
                    ->title(__('filament-panels::pages/dashboard.alert.onboard-simple.title'))
                    ->description(__('filament-panels::pages/dashboard.alert.onboard-simple.description'))
                    ->warning()
                    ->border()
                    ->columnSpanFull()
                    ->visible(! auth()->user()->hasSetupFinancial())
                    ->actions([
                        Action::make('setup')
                            ->label(__('filament-panels::pages/dashboard.alert.onboard-simple.button'))
                            ->color('warning')
                            ->link()
                            ->button()
                            ->url(route('filament.user.financial-setup'))
                            ->icon('heroicon-m-arrow-long-right')
                            ->iconPosition('after'),
                    ]),

            ]);
    }
}
