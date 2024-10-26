@php
    $totalProgress = $getState();

    $backgroundColor = match (true) {
        $totalProgress >= 80 => "bg-rose-500 dark:bg-rose-400",
        $totalProgress >= 50 => "bg-amber-500 dark:bg-amber-400",
        default => "bg-primary-500 dark:bg-primary-400"
    }
@endphp
<div class="w-full px-4">
    <div class="w-full h-4 bg-gray-200 rounded-full dark:bg-gray-700">
        <div class="h-4 rounded-full {{ $backgroundColor }}" style="width: {{ min($totalProgress, 100) }}%"></div>
    </div>
</div>
