<?php

namespace App\Filament\Pages;

use App\Enums\Period;
use App\Filament\Tables\Filters\PeriodFilter;
use CodeWithDennis\SimpleAlert\Components\Infolists\SimpleAlert;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;

class Dashboard extends BaseDashboard implements HasInfolists
{
    use InteractsWithInfolists, HasFiltersForm;

    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.pages.dashboard';

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Select::make('period')
                            ->options(Period::toArray())
                            ->live(),
                        DatePicker::make('startDate')
                            ->visible(fn (Get $get): bool => $get('period') == Period::Custom->value),
                        DatePicker::make('endDate')
                            ->visible(fn (Get $get): bool => $get('period') == Period::Custom->value),
                    ])
                    ->columns(3),
            ])
            ->live();
    }

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
                    ->visible(! auth()->user()->hasSetupFinancial())
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
