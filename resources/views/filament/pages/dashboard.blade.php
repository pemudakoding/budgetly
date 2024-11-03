<x-filament-panels::page>
    {{ $this->makeInfolist() }}

    {{ $this->filtersForm }}

    @livewire(\App\Filament\Widgets\Dashboard\AmountOverview::class)
    @livewire(\App\Filament\Widgets\Dashboard\AccountSummary::class)
</x-filament-panels::page>
