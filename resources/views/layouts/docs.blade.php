@extends('layouts.app', ['hideAppShell' => true])

@section('title', 'Flow Forms Documentation')

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

@php
    $navigationItems = \App\Helpers\MarkdownHelper::getNavigationItems();
@endphp

{{-- Override navigation for docs --}}
@section('navigation')
    <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
        <flux:navbar class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <flux:navbar.item>
                <flux:link href="/" class="flex items-center space-x-2">
                    <flux:heading size="lg">Flow Forms</flux:heading>
                    <flux:badge size="sm" color="zinc">Docs</flux:badge>
                </flux:link>
            </flux:navbar.item>

            <flux:spacer />

            <flux:navbar.item>
                <flux:link href="https://flowforms.io" variant="subtle" size="sm">
                    ← Return to Main Site
                </flux:link>
            </flux:navbar.item>
        </flux:navbar>
    </header>
@endsection

{{-- Main content layout with sidebar --}}
@section('content')
    <div class="flex">
        <!-- Sidebar -->
        <nav class="hidden lg:flex lg:w-64 lg:flex-col lg:bg-gray-50 lg:border-r lg:border-gray-200 dark:lg:bg-gray-800 dark:lg:border-gray-700">
            <div class="flex-1 px-4 py-6 overflow-y-auto">
                <div class="space-y-8">
                    {{-- Root level files --}}
                    @if(isset($navigationItems['_root']) && !empty($navigationItems['_root']))
                        <div>
                            <flux:subheading class="mb-3">Documentation</flux:subheading>
                            <flux:navlist>
                                @foreach($navigationItems['_root'] as $item)
                                    <flux:navlist.item
                                        href="{{ $item['url'] }}"
                                        :current="request()->is($item['filename'])">
                                        {{ $item['title'] }}
                                    </flux:navlist.item>
                                @endforeach
                            </flux:navlist>
                        </div>
                    @endif

                    {{-- Folder sections --}}
                    @foreach($navigationItems as $key => $section)
                        @if($key !== '_root' && $section['type'] === 'folder')
                            <div>
                                <flux:subheading class="mb-3">{{ $section['title'] }}</flux:subheading>
                                <flux:navlist>
                                    @foreach($section['items'] as $item)
                                        @php
                                            $isActive = request()->is($item['folder'].'/'.$item['filename']);
                                        @endphp
                                        <flux:navlist.item
                                            href="{{ $item['url'] }}"
                                            :current="$isActive">
                                            {{ $item['title'] }}
                                        </flux:navlist.item>
                                    @endforeach
                                </flux:navlist>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </nav>

        <!-- Main content -->
        <div class="flex-1 overflow-hidden">
            <div class="py-8 px-4 sm:px-6 lg:px-8">
                @yield('docs-content')
            </div>
        </div>
    </div>

    <!-- Mobile sidebar -->
    <div class="lg:hidden" x-data="{ open: false }">
        <!-- Mobile menu button -->
        <div class="fixed top-4 left-4 z-50">
            <flux:button @click="open = !open" size="sm" variant="outline" class="shadow-md">
                <flux:icon.bars-3 class="h-5 w-5" />
            </flux:button>
        </div>

        <!-- Mobile menu overlay -->
        <div x-show="open" @click="open = false" class="fixed inset-0 z-40 bg-black bg-opacity-25"></div>

        <!-- Mobile sidebar -->
        <div x-show="open" class="fixed top-0 left-0 z-50 w-64 h-full bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 overflow-y-auto">
            <div class="p-4">
                <div class="flex items-center justify-between mb-6">
                    <flux:heading size="base">Navigation</flux:heading>
                    <flux:button @click="open = false" size="sm" variant="ghost">
                        <flux:icon.x-mark class="h-5 w-5" />
                    </flux:button>
                </div>

                <!-- Same navigation structure as desktop -->
                <div class="space-y-6">
                    {{-- Root level files --}}
                    @if(isset($navigationItems['_root']) && !empty($navigationItems['_root']))
                        <div>
                            <flux:subheading class="mb-3">Documentation</flux:subheading>
                            <flux:navlist>
                                @foreach($navigationItems['_root'] as $item)
                                    <flux:navlist.item
                                        href="{{ $item['url'] }}"
                                        :current="request()->is($item['filename'])"
                                        @click="open = false">
                                        {{ $item['title'] }}
                                    </flux:navlist.item>
                                @endforeach
                            </flux:navlist>
                        </div>
                    @endif

                    {{-- Folder sections --}}
                    @foreach($navigationItems as $key => $section)
                        @if($key !== '_root' && $section['type'] === 'folder')
                            <div>
                                <flux:subheading class="mb-3">{{ $section['title'] }}</flux:subheading>
                                <flux:navlist>
                                    @foreach($section['items'] as $item)
                                        @php
                                            $isActive = request()->is($item['folder'].'/'.$item['filename']);
                                        @endphp
                                        <flux:navlist.item
                                            href="{{ $item['url'] }}"
                                            :current="$isActive"
                                            @click="open = false">
                                            {{ $item['title'] }}
                                        </flux:navlist.item>
                                    @endforeach
                                </flux:navlist>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
