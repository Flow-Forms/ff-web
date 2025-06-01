@props([
    'title' => 'Flow Forms Documentation'
])

@php
    $navigationItems = \App\Helpers\MarkdownHelper::getNavigationItems();
@endphp

<x-app-layout :hide-app-shell="true">
    <x-slot name="title">{{ $title }}</x-slot>
    
    @push('styles')
    <style>
        /* Override app layout styles for docs */
        body > div.min-h-screen {
            background-color: white !important;
        }
        body > div.min-h-screen.dark {
            background-color: rgb(17 24 39) !important;
        }
    </style>
    @endpush
    
    {{-- Custom navigation for docs --}}
    <x-slot name="navigation">
        <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
            <flux:navbar class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <flux:navbar.item>
                    <flux:link href="/" class="flex items-center space-x-2">
                        <flux:heading size="lg">Flow Forms</flux:heading>
                        <flux:badge size="sm" color="zinc">Docs</flux:badge>
                    </flux:link>
                </flux:navbar.item>
                
                <flux:spacer />
                
                {{-- Search --}}
                <flux:navbar.item class="hidden md:block">
                    <x-docs.search class="w-96" />
                </flux:navbar.item>
                
                <flux:navbar.item>
                    <flux:link href="https://flowforms.io" variant="subtle" size="sm">
                        ← Return to Main Site
                    </flux:link>
                </flux:navbar.item>
            </flux:navbar>
        </header>
    </x-slot>
    
    {{-- Main content with sidebar layout --}}
    <div class="flex">
        <!-- Sidebar -->
        <x-docs.sidebar :navigation-items="$navigationItems" />

        <!-- Main content -->
        <div class="flex-1 overflow-hidden">
            <div class="py-8 px-4 sm:px-6 lg:px-8">
                {{ $slot }}
            </div>
        </div>
    </div>

    <!-- Mobile sidebar -->
    <x-docs.mobile-sidebar :navigation-items="$navigationItems" />
</x-app-layout>