<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;
use Illuminate\Contracts\Support\Htmlable;

class FinancialSetup extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    public static function getClusterBreadcrumb(): ?string
    {
        return __('filament-panels::pages/financial-setup.title');
    }

    public function getTitle(): string|Htmlable
    {
        return __('filament-panels::pages/financial-setup.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament-panels::pages/financial-setup.title');
    }
}
