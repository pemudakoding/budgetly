<x-filament-panels::page>
    <x-filament-panels::resources.tabs />

    <x-filament::section collapsible="true">
        <x-slot name="heading">
            Filter
        </x-slot>

        {{ $this->form }}
    </x-filament::section>

    @livewire(\App\Filament\Resources\Budgeting\ExpenseResource\Widgets\ExpenseWidget::class)

    {{ $this->table }}
</x-filament-panels::page>
