<div class="text-sm {{ $getState() > 100 ? 'text-rose-500 dark:text-rose-400' : '' }}">
    {{ number_format($getState(), 2) }}%
</div>
