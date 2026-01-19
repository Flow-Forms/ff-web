<?php
use App\Helpers\MarkdownHelper;

// Find the index document dynamically
$indexPath = MarkdownHelper::getIndexDocumentPath();

if (!$indexPath) {
    // Fallback if no index document is found
    $title = 'Flow Forms Documentation';
    $subtitle = 'Welcome to Flow Forms';
    $sections = [];
    $help = null;
} else {
    // Parse the index document with frontmatter
    $data = MarkdownHelper::parseWithFrontmatter($indexPath);
    $frontmatter = $data['frontmatter'];
    $title = $frontmatter['title'] ?? 'Flow Forms Documentation';
    $subtitle = $frontmatter['subtitle'] ?? '';
    $sections = $frontmatter['sections'] ?? [];
    $help = $frontmatter['help'] ?? null;
}
?>

<x-layouts.app>
    <x-slot name="title">{{ $title }}</x-slot>
    
<div class="max-w-6xl mx-auto">
    <div class="text-center py-12">
        <flux:heading size="2xl" class="mb-4">{{ $title }}</flux:heading>
        @if($subtitle)
            <flux:subheading size="lg" class="mb-8">{{ $subtitle }}</flux:subheading>
        @endif
        
        @foreach($sections as $section)
            <div class="mt-12">
                <flux:heading size="xl" class="mb-6 text-left">{{ $section['title'] }}</flux:heading>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-{{ $section['columns'] ?? 2 }} gap-6">
                    @foreach($section['items'] as $item)
                        <flux:card class="hover:shadow-lg transition-shadow">
                            <div class="p-6">
                                <flux:heading size="lg" class="mb-3">
                                    <flux:link href="{{ $item['url'] }}" class="text-inherit">{{ $item['title'] }}</flux:link>
                                </flux:heading>
                                <flux:text>{{ $item['description'] }}</flux:text>
                            </div>
                        </flux:card>
                    @endforeach
                </div>
            </div>
        @endforeach
        
        @if($help)
            <div class="mt-16 mb-8 p-8 bg-gray-50 dark:bg-gray-900 rounded-lg">
                <flux:heading size="xl" class="mb-4">{{ $help['title'] }}</flux:heading>
                <div class="flex flex-wrap gap-6 justify-center">
                    @foreach($help['links'] as $index => $link)
                        @if($index > 0)
                            <flux:text>•</flux:text>
                        @endif
                        @if(isset($link['url']))
                            <flux:link href="{{ $link['url'] }}" class="text-blue-600 dark:text-blue-400 hover:underline">{{ $link['text'] }}</flux:link>
                        @else
                            <flux:text>{{ $link['text'] }}</flux:text>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
</x-layouts.app>