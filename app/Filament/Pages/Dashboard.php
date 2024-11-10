<?php

namespace App\Filament\Pages;

use App\Enums\Period;
use App\Filament\Widgets\Dashboard\AccountSummary;
use App\Filament\Widgets\Dashboard\AmountOverview;
use CodeWithDennis\SimpleAlert\Components\Infolists\SimpleAlert;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Illuminate\Contracts\Support\Htmlable;

class Dashboard extends BaseDashboard implements HasInfolists
{
    use HasFiltersForm, InteractsWithInfolists;

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
                            ->default(Period::Today->value)
                            ->label(__('filament-forms::components.text_input.label.period.name'))
                            ->afterStateUpdated(function (string $state, Set $set) {
                                if ($state !== Period::Custom->value) {
                                    $set('startDate', null);
                                    $set('endDate', null);
                                }
                            }),
                        DatePicker::make('startDate')
                            ->label(__('filament-forms::components.text_input.label.period.start_date'))
                            ->visible(fn (Get $get): bool => $get('period') == Period::Custom->value),
                        DatePicker::make('endDate')
                            ->label(__('filament-forms::components.text_input.label.period.end_date'))
                            ->visible(fn (Get $get): bool => $get('period') == Period::Custom->value),
                    ])
                    ->columns(3),
            ])
            ->live();
    }

    public function getWidgets(): array
    {
        return [
            AmountOverview::class,
            AccountSummary::class,
        ];
    }

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
