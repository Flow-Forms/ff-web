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
        <div class="relative w-full rounded-xl overflow-hidden shadow-lg mb-8" style="padding-bottom: 56.25%;" x-data="videoPlayer()">
            @if($video->getEmbedUrl())
                <iframe
                    x-ref="videoFrame"
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

        {{-- Video Title & Duration --}}
        <div class="mb-6">
            <flux:heading size="2xl" class="mb-3">{{ $video->title }}</flux:heading>

            @if($video->duration_seconds)
                <div class="flex items-center gap-2 text-zinc-500 dark:text-zinc-400">
                    <flux:icon.clock class="size-4" />
                    <span>{{ $video->getFormattedDuration() }}</span>
                </div>
            @endif
        </div>

        {{-- Summary --}}
        @if($video->description)
            <div class="mb-8" x-data="{ expanded: false }">
                <flux:card>
                    <div class="p-6">
                        <flux:heading size="sm" class="text-zinc-500 dark:text-zinc-400 uppercase tracking-wide mb-4">Summary</flux:heading>

                        {{-- Teaser (always visible) --}}
                        <div x-show="!expanded" class="text-zinc-700 dark:text-zinc-300">
                            {{ $video->getSummaryTeaser() }}
                            @if($video->hasSummaryBeyondTeaser())
                                <button
                                    type="button"
                                    x-on:click="expanded = true"
                                    class="text-blue-600 dark:text-blue-400 hover:underline ml-1"
                                >
                                    Read more
                                </button>
                            @endif
                        </div>

                        {{-- Full content (when expanded) --}}
                        <div
                            x-show="expanded"
                            x-collapse
                        >
                            <div class="prose prose-zinc dark:prose-invert max-w-none">
                                {!! $video->description !!}
                            </div>
                            <button
                                type="button"
                                x-on:click="expanded = false"
                                class="text-blue-600 dark:text-blue-400 hover:underline mt-3 text-sm"
                            >
                                Show less
                            </button>
                        </div>
                    </div>
                </flux:card>
            </div>
        @endif

        {{-- Chapters --}}
        @if($video->hasChapters())
            <div class="mb-8" x-data="{ expanded: false }">
                <div class="flex items-center justify-between mb-4">
                    <flux:heading size="lg">Chapters</flux:heading>
                    @if(count($video->chapters) > 5)
                        <flux:button size="sm" variant="ghost" x-on:click="expanded = !expanded">
                            <span x-show="!expanded">Show all</span>
                            <span x-show="expanded">Show less</span>
                        </flux:button>
                    @endif
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($video->chapters as $index => $chapter)
                        <button
                            type="button"
                            x-show="expanded || {{ $index }} < 6"
                            x-on:click="$dispatch('seek-video', { time: {{ $chapter['start'] }} })"
                            class="flex items-center gap-3 p-3 rounded-lg bg-zinc-50 dark:bg-zinc-800 hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors text-left group"
                        >
                            <span class="flex-shrink-0 w-16 text-sm font-mono text-zinc-500 dark:text-zinc-400">
                                {{ gmdate($chapter['start'] >= 3600 ? 'H:i:s' : 'i:s', $chapter['start']) }}
                            </span>
                            <span class="font-medium text-zinc-900 dark:text-zinc-100 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                {{ $chapter['title'] }}
                            </span>
                        </button>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Transcript --}}
        @if($video->hasTranscript())
            <div class="mb-12" x-data="{ expanded: false }">
                <flux:card>
                    <div class="p-6">
                        <button
                            type="button"
                            x-on:click="expanded = !expanded"
                            x-bind:aria-expanded="expanded"
                            class="w-full flex items-center justify-between text-left"
                        >
                            <flux:heading size="sm" class="text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Transcript</flux:heading>
                            <flux:icon.chevron-down
                                class="size-5 text-zinc-400 transition-transform duration-200"
                                x-bind:class="{ 'rotate-180': expanded }"
                            />
                        </button>
                        <div
                            x-show="expanded"
                            x-collapse
                            class="mt-4"
                        >
                            <div class="prose prose-zinc dark:prose-invert max-w-none whitespace-pre-wrap">{{ $video->transcript }}</div>
                        </div>
                    </div>
                </flux:card>
            </div>
        @endif

        {{-- More Videos --}}
        @if($otherVideos->isNotEmpty())
            <div class="border-t border-zinc-200 dark:border-zinc-700 pt-12">
                <flux:heading size="xl" class="mb-6">More Videos</flux:heading>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($otherVideos as $otherVideo)
                        <a href="/video/{{ $otherVideo->slug }}" class="group block">
                            <flux:card class="overflow-hidden hover:shadow-lg transition-shadow h-full">
                                <x-video-thumbnail :video="$otherVideo" size="sm" />

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

    @if($video->hasChapters())
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('videoPlayer', () => ({
                    init() {
                        this.$el.addEventListener('seek-video', (event) => {
                            this.seekTo(event.detail.time);
                        });
                    },
                    seekTo(seconds) {
                        const iframe = this.$refs.videoFrame;
                        if (iframe) {
                            // Bunny Stream player supports postMessage API for seeking
                            iframe.contentWindow.postMessage({
                                event: 'seek',
                                time: seconds
                            }, 'https://iframe.mediadelivery.net');
                        }
                    }
                }));
            });
        </script>
    @endif
</x-layouts.app>
