<?php

namespace App\Livewire;

use CodeWithDennis\SimpleAlert\Components\Infolists\SimpleAlert;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;

class OnboardIndex extends Page implements HasInfolists
{
    use InteractsWithInfolists;

    protected static string $view = 'livewire.onboard-index';

    protected ?string $heading = '';

    protected function makeInfolist(): Infolist
    {
        return Infolist::make()
            ->schema([
                SimpleAlert::make('example')
                    ->title('Just One Step to Unlock!')
                    ->description('Complete your financial setup by adding your accounts, expenses, and income to get ready to explore our features!')
                    ->info()
                    ->border()
                    ->columnSpanFull()
                    ->visible(! auth()->user()->hasSetupFinancial()),
            ]);
    }
}
