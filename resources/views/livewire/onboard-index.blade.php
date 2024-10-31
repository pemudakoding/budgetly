<x-filament-panels::page>
    {{ $this->makeInfolist }}

    <form wire:submit="save">
        {{ $this->form }}
    </form>
</x-filament-panels::page>
