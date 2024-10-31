<x-filament-panels::page>
    {{ $this->makeInfolist }}

    <form wire:submit="submit">
        {{ $this->form }}
    </form>
</x-filament-panels::page>
