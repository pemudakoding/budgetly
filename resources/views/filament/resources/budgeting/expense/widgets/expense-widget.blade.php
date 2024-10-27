<x-filament-widgets::widget class="fi-wi-stats-overview">
    <div class="fi-wi-stats-overview-stats-ctn grid gap-6 md:grid-cols-2">
        @foreach ($this->getCachedStats() as $stat)
            {{ $stat }}
        @endforeach
    </div>
</x-filament-widgets::widget>
