<x-filament-panels::page>
    <x-filament-panels::resources.tabs />

    <x-filament::section collapsible="true">
        <x-slot name="heading">
            Filter Expense
        </x-slot>

        {{ $this->form  }}
    </x-filament::section>

    {{ $this->table }}
</x-filament-panels::page>