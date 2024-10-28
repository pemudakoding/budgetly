<?php

namespace App\Filament\Pages;

use CodeWithDennis\SimpleAlert\Components\Infolists\SimpleAlert;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;

class Dashboard extends Page implements HasInfolists
{
    use InteractsWithInfolists;

    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.pages.dashboard';

    protected function makeInfolist(): Infolist
    {
        return Infolist::make()
            ->schema([
                SimpleAlert::make('example')
                    ->title('Just One Step to Unlock!')
                    ->description('Complete your financial setup by adding your accounts, expenses, and income!')
                    ->warning()
                    ->border()
                    ->columnSpanFull()
                    ->actions([
                        Action::make('setup')
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
