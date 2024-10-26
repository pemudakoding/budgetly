<x-filament-panels::page>
    <x-filament::section collapsible="true" collapsed="true">
        <x-slot name="heading">
            Filter Expense
        </x-slot>
        {{ $this->form  }}
    </x-filament::section>

    {{ $this->table }}
</x-filament-panels::page>