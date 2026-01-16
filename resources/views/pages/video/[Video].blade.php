<?php
use App\Models\Video;
use function Laravel\Folio\{name, render};

name('video.show');

render(function (Video $video) {
    if (!$video->is_published || !$video->isReady()) {
        abort(404);
    }

    return view('pages.video.[Video]', ['video' => $video]);
});
?>

<x-layouts.app>
    <x-slot name="title">{{ $video->title }} - Flow Forms Documentation</x-slot>

    <div class="max-w-4xl mx-auto">
        {{-- Back link --}}
        <div class="mb-6">
            <flux:link href="/video" class="inline-flex items-center gap-1 text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200">
                <flux:icon.arrow-left class="size-4" />
                Back to Videos
            </flux:link>
        </div>

        {{-- Video Player --}}
        <div class="aspect-video bg-black rounded-lg overflow-hidden mb-6">
            @if($video->getEmbedUrl())
                <iframe
                    src="{{ $video->getEmbedUrl() }}?autoplay=false&preload=true"
                    loading="lazy"
                    class="w-full h-full border-0"
                    allow="accelerometer; gyroscope; autoplay; encrypted-media; picture-in-picture"
                    allowfullscreen
                ></iframe>
            @else
                <div class="w-full h-full flex items-center justify-center">
                    <div class="text-center text-white">
                        <flux:icon.exclamation-circle class="size-12 mx-auto mb-3 opacity-50" />
                        <flux:text>Video not available</flux:text>
                    </div>
                </div>
            @endif
        </div>

        {{-- Video Info --}}
        <div class="mb-8">
            <flux:heading size="2xl" class="mb-2">{{ $video->title }}</flux:heading>
            @if($video->duration_seconds)
                <flux:text class="text-zinc-500 dark:text-zinc-400">
                    Duration: {{ $video->getFormattedDuration() }}
                </flux:text>
            @endif
        </div>

        @if($video->description)
            <div class="prose prose-lg dark:prose-invert prose-gray max-w-none">
                <p>{{ $video->description }}</p>
            </div>
        @endif
    </div>
</x-layouts.app>
