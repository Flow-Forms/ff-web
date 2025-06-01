@props([
    'title' => 'Flow Forms Documentation'
])

@php
    $navigationItems = \App\Helpers\MarkdownHelper::getNavigationItems();
@endphp

<x-app-layout :hide-app-shell="true">
    <x-slot name="title">{{ $title }}</x-slot>

    <x-slot name="navigation">
        <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center">
                        <flux:link href="/" class="flex items-center space-x-2">
                            <flux:heading size="xl" class="text-gray-900 dark:text-white">Flow Forms</flux:heading>
                            <flux:badge size="sm" color="zinc">Docs</flux:badge>
                        </flux:link>
                    </div>

                    <div class="flex-1 max-w-2xl mx-8 hidden md:block">
                        <x-docs.search />
                    </div>

                    <div class="flex items-center space-x-4">
                        <x-docs.theme-toggle />

                        <flux:link href="https://flowforms.io" variant="subtle" size="sm" class="text-gray-600 dark:text-gray-300">
                            ← Return to Main Site
                        </flux:link>
                    </div>
                </div>
            </div>
        </header>
    </x-slot>

    <div class="flex bg-gray-50 dark:bg-gray-900">
        <x-docs.sidebar :navigation-items="$navigationItems" />

        <div class="flex-1 bg-white dark:bg-gray-900">
            <div class="py-8 px-4 sm:px-6 lg:px-8">
                {{ $slot }}
            </div>
        </div>
    </div>

    <x-docs.mobile-sidebar :navigation-items="$navigationItems" />
</x-app-layout>
