@props([
    'title' => 'Flow Forms Documentation'
])

@php
    $navigationItems = \App\Helpers\MarkdownHelper::getNavigationItems();
@endphp

<x-app-layout :hide-app-shell="true">
    <x-slot name="title">{{ $title }}</x-slot>
    
    {{-- Custom navigation for docs --}}
    <x-slot name="navigation">
        <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    {{-- Logo --}}
                    <div class="flex items-center">
                        <flux:link href="/" class="flex items-center space-x-2">
                            <flux:heading size="lg" class="text-gray-900 dark:text-white">Flow Forms</flux:heading>
                            <flux:badge size="sm" color="zinc">Docs</flux:badge>
                        </flux:link>
                    </div>
                    
                    {{-- Right side items --}}
                    <div class="flex items-center space-x-4">
                        {{-- Search --}}
                        <div class="hidden md:block">
                            <x-docs.search />
                        </div>
                        
                        {{-- Theme Toggle --}}
                        <x-docs.theme-toggle />
                        
                        {{-- Return to Main Site --}}
                        <flux:link href="https://flowforms.io" variant="subtle" size="sm" class="text-gray-600 dark:text-gray-300">
                            ← Return to Main Site
                        </flux:link>
                    </div>
                </div>
            </div>
        </header>
    </x-slot>
    
    {{-- Main content with sidebar layout --}}
    <div class="flex bg-gray-50 dark:bg-gray-900">
        <!-- Sidebar -->
        <x-docs.sidebar :navigation-items="$navigationItems" />

        <!-- Main content -->
        <div class="flex-1 bg-white dark:bg-gray-900">
            <div class="py-8 px-4 sm:px-6 lg:px-8">
                {{ $slot }}
            </div>
        </div>
    </div>

    <!-- Mobile sidebar -->
    <x-docs.mobile-sidebar :navigation-items="$navigationItems" />
</x-app-layout>