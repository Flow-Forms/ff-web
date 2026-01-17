<?php
use App\Models\Video;
use Illuminate\Support\Facades\Gate;
use function Laravel\Folio\{name, render};

name('video.show');

render(function (Video $video) {
    // Video must be ready to view
    if (!$video->isReady()) {
        abort(404);
    }

    // Allow viewing if published OR if user can manage videos (for preview)
    if (!$video->is_published && !Gate::allows('manage-videos')) {
        abort(404);
    }

    $otherVideos = Video::query()
        ->published()
        ->where('id', '!=', $video->id)
        ->ordered()
        ->limit(4)
        ->get();

    return view('pages.video.[Video]', [
        'video' => $video,
        'otherVideos' => $otherVideos,
        'isPreview' => !$video->is_published,
    ]);
});
?>

<x-layouts.app :show-sidebar="false">
    <x-slot name="title">{{ $video->title }} - Flow Forms Documentation</x-slot>

    <div class="max-w-5xl mx-auto">
        @if($isPreview)
            <flux:callout variant="warning" icon="eye" class="mb-6">
                <flux:callout.heading>Preview Mode</flux:callout.heading>
                <flux:callout.text>This video is not published. Only you can see this page.</flux:callout.text>
            </flux:callout>
        @endif

        {{-- Breadcrumb --}}
        <nav class="mb-6">
            <ol class="flex items-center gap-2 text-sm">
                <li>
                    <a href="{{ route('dashboard') }}" class="text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200 transition-colors">
                        Videos
                    </a>
                </li>
                <li class="text-zinc-400 dark:text-zinc-500">/</li>
                <li class="text-zinc-900 dark:text-zinc-100 font-medium truncate">{{ $video->title }}</li>
            </ol>
        </nav>

        {{-- Video Player --}}
        <div class="relative w-full rounded-xl overflow-hidden shadow-lg mb-8" style="padding-bottom: 56.25%;">
            @if($video->getEmbedUrl())
                <iframe
                    src="{{ $video->getEmbedUrl() }}?autoplay=false&preload=true"
                    loading="lazy"
                    style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: none;"
                    allow="accelerometer; gyroscope; autoplay; encrypted-media; picture-in-picture"
                    allowfullscreen
                ></iframe>
            @else
                <div class="absolute inset-0 w-full h-full flex items-center justify-center bg-zinc-900">
                    <div class="text-center text-white">
                        <flux:icon.exclamation-circle class="size-12 mx-auto mb-3 opacity-50" />
                        <flux:text class="text-zinc-400">Video not available</flux:text>
                    </div>
                </div>
            @endif
        </div>

        {{-- Video Info --}}
        <div class="mb-12">
            <flux:heading size="2xl" class="mb-3">{{ $video->title }}</flux:heading>

            @if($video->duration_seconds)
                <div class="flex items-center gap-2 text-zinc-500 dark:text-zinc-400 mb-4">
                    <flux:icon.clock class="size-4" />
                    <span>{{ $video->getFormattedDuration() }}</span>
                </div>
            @endif

            @if($video->description)
                <flux:card class="mt-6">
                    <div class="p-6">
                        <flux:heading size="sm" class="mb-3 text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">About this video</flux:heading>
                        <div class="prose prose-zinc dark:prose-invert max-w-none">
                            {!! $video->description !!}
                        </div>
                    </div>
                </flux:card>
            @endif
        </div>

        {{-- More Videos --}}
        @if($otherVideos->isNotEmpty())
            <div class="border-t border-zinc-200 dark:border-zinc-700 pt-12">
                <flux:heading size="xl" class="mb-6">More Videos</flux:heading>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($otherVideos as $otherVideo)
                        <a href="/video/{{ $otherVideo->slug }}" class="group block">
                            <flux:card class="overflow-hidden hover:shadow-lg transition-shadow h-full">
                                {{-- Thumbnail --}}
                                <div class="aspect-video bg-zinc-100 dark:bg-zinc-800 relative overflow-hidden">
                                    @if($otherVideo->thumbnail_url)
                                        <img
                                            src="{{ $otherVideo->thumbnail_url }}"
                                            alt="{{ $otherVideo->title }}"
                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                        >
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <flux:icon.play-circle class="size-10 text-zinc-300 dark:text-zinc-600" />
                                        </div>
                                    @endif

                                    {{-- Duration Badge --}}
                                    @if($otherVideo->duration_seconds)
                                        <div class="absolute bottom-2 right-2 px-1.5 py-0.5 bg-black/75 rounded text-white text-xs font-medium">
                                            {{ $otherVideo->getFormattedDuration() }}
                                        </div>
                                    @endif

                                    {{-- Play overlay --}}
                                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity bg-black/30">
                                        <flux:icon.play-circle class="size-10 text-white" />
                                    </div>
                                </div>

                                {{-- Content --}}
                                <div class="p-3">
                                    <flux:text class="font-medium text-sm group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors line-clamp-2">
                                        {{ $otherVideo->title }}
                                    </flux:text>
                                </div>
                            </flux:card>
                        </a>
                    @endforeach
                </div>

                <div class="mt-6 text-center">
                    <flux:button href="{{ route('dashboard') }}" variant="ghost" icon-trailing="arrow-right">
                        View all videos
                    </flux:button>
                </div>
            </div>
        @endif
    </div>
</x-layouts.app>
