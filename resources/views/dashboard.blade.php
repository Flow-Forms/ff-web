<?php
use App\Models\Video;

$videos = Video::query()->published()->ordered()->get();
?>

<x-layouts.app :show-sidebar="false" title="Video Tutorials">
    <div class="max-w-4xl mx-auto">
        <div class="text-center mb-10">
            <flux:heading size="2xl" class="mb-4">Video Tutorials</flux:heading>
            <flux:subheading size="lg">Learn Flow Forms through step-by-step video guides.</flux:subheading>
        </div>

        @if($videos->isEmpty())
            <flux:card>
                <div class="p-12 text-center">
                    <flux:icon.film class="size-16 mx-auto text-zinc-300 dark:text-zinc-600 mb-4" />
                    <flux:heading size="lg" class="mb-2">No videos available yet</flux:heading>
                    <flux:text>Check back soon for video tutorials.</flux:text>
                </div>
            </flux:card>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($videos as $video)
                    <a href="/video/{{ $video->slug }}" class="group block">
                        <flux:card class="overflow-hidden hover:shadow-lg transition-shadow">
                            {{-- Thumbnail --}}
                            <div class="aspect-video bg-zinc-100 dark:bg-zinc-800 relative overflow-hidden">
                                @if($video->thumbnail_url)
                                    <img
                                        src="{{ $video->thumbnail_url }}"
                                        alt="{{ $video->title }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                    >
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <flux:icon.play-circle class="size-16 text-zinc-300 dark:text-zinc-600" />
                                    </div>
                                @endif

                                {{-- Duration Badge --}}
                                @if($video->duration_seconds)
                                    <div class="absolute bottom-2 right-2 px-2 py-1 bg-black/75 rounded text-white text-xs font-medium">
                                        {{ $video->getFormattedDuration() }}
                                    </div>
                                @endif

                                {{-- Play overlay --}}
                                <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity bg-black/30">
                                    <flux:icon.play-circle class="size-16 text-white" />
                                </div>
                            </div>

                            {{-- Content --}}
                            <div class="p-4">
                                <flux:heading size="lg" class="group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                    {{ $video->title }}
                                </flux:heading>
                                @if($video->description)
                                    <flux:text class="mt-2 line-clamp-2">{!! $video->getDescriptionExcerpt() !!}</flux:text>
                                @endif
                            </div>
                        </flux:card>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts.app>
