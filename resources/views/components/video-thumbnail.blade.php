@props([
    'video',
    'size' => 'default',
])

@php
    $iconSize = match($size) {
        'sm' => 'size-10',
        default => 'size-16',
    };

    $badgeClasses = match($size) {
        'sm' => 'px-1.5 py-0.5',
        default => 'px-2 py-1',
    };
@endphp

<div class="aspect-video bg-zinc-100 dark:bg-zinc-800 relative overflow-hidden" x-data="{ imageError: false }">
    @if($video->thumbnail_url)
        <img
            src="{{ $video->thumbnail_url }}"
            alt="{{ $video->title }}"
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
            referrerpolicy="no-referrer-when-downgrade"
            x-show="!imageError"
            x-on:error="imageError = true"
        >
        <div x-show="imageError" x-cloak class="w-full h-full flex items-center justify-center">
            <flux:icon.play-circle class="{{ $iconSize }} text-zinc-300 dark:text-zinc-600" />
        </div>
    @else
        <div class="w-full h-full flex items-center justify-center">
            <flux:icon.play-circle class="{{ $iconSize }} text-zinc-300 dark:text-zinc-600" />
        </div>
    @endif

    {{-- Duration Badge --}}
    @if($video->duration_seconds)
        <div class="absolute bottom-2 right-2 {{ $badgeClasses }} bg-black/75 rounded text-white text-xs font-medium">
            {{ $video->getFormattedDuration() }}
        </div>
    @endif

    {{-- Play overlay --}}
    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity bg-black/30">
        <flux:icon.play-circle class="{{ $iconSize }} text-white" />
    </div>
</div>
