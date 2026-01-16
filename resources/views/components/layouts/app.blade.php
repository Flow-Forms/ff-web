@props([
    'title' => 'Flow Forms Documentation',
    'showSidebar' => true
])

@php
    $navigationItems = $showSidebar ? \App\Helpers\MarkdownHelper::getNavigationItems() : [];
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @fluxAppearance

        <!-- Fathom - beautiful, simple website analytics -->
        <script src="https://cdn.usefathom.com/script.js" data-site="KHJGOLPI" defer></script>
    </head>

    <body class="font-sans antialiased bg-white dark:bg-zinc-900">
        <div class="min-h-screen">
            {{-- Fixed Header --}}
            <header class="sticky top-0 z-50 h-16 flex items-center border-b border-zinc-200 dark:border-zinc-800 bg-white/95 dark:bg-zinc-900/95 backdrop-blur supports-[backdrop-filter]:bg-white/80 dark:supports-[backdrop-filter]:bg-zinc-900/80">
                <div class="w-full max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center gap-4">
                    @if($showSidebar)
                        {{-- Mobile menu button --}}
                        <button
                            type="button"
                            class="lg:hidden -ml-2 p-2 text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200"
                            x-data
                            x-on:click="$dispatch('toggle-sidebar')"
                        >
                            <flux:icon.bars-3 class="size-6" />
                        </button>
                    @endif

                    {{-- Logo --}}
                    <a href="/" class="flex items-center shrink-0">
                        <img src="/images/flow-forms-logo.svg" alt="Flow Forms" class="h-9 w-auto dark:hidden" />
                        <img src="/images/flow-forms-logo-dark.svg" alt="Flow Forms" class="h-9 w-auto hidden dark:block" />
                    </a>

                    @if($showSidebar)
                        {{-- Docs badge --}}
                        <span class="hidden sm:inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400">
                            Docs
                        </span>
                    @endif

                    {{-- Spacer --}}
                    <div class="flex-1"></div>

                    @if($showSidebar)
                        {{-- Search --}}
                        <div class="hidden md:block w-64 lg:w-96 xl:w-[28rem]">
                            <x-docs.search />
                        </div>

                        {{-- Spacer --}}
                        <div class="flex-1"></div>
                    @endif

                    {{-- Right actions --}}
                    <div class="flex items-center gap-1">
                        <x-docs.theme-toggle />

                        <a
                            href="https://flowforms.io"
                            target="_blank"
                            class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 text-sm text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100"
                        >
                            Main Site
                            <flux:icon.arrow-top-right-on-square class="size-4" />
                        </a>

                        @auth
                            <flux:dropdown position="bottom" align="end">
                                <flux:profile :name="Auth::user()->name" />

                                <flux:menu>
                                    <flux:menu.item href="{{ route('profile.show') }}" icon="user">Profile</flux:menu.item>
                                    <flux:menu.separator />
                                    <flux:menu.item
                                        icon="arrow-right-start-on-rectangle"
                                        x-data
                                        x-on:click.prevent="$refs.logoutForm.submit()"
                                    >
                                        Logout
                                    </flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>

                            <form x-ref="logoutForm" method="POST" action="{{ route('logout') }}" class="hidden">
                                @csrf
                            </form>
                        @endauth
                    </div>
                </div>
            </header>

            @if($showSidebar)
                <div class="max-w-screen-2xl mx-auto flex">
                    {{-- Mobile sidebar --}}
                    <div
                        x-data="{ open: false }"
                        x-on:toggle-sidebar.window="open = !open"
                        x-on:close-sidebar.window="open = false"
                        x-on:keydown.escape.window="open = false"
                        class="lg:hidden"
                    >
                        {{-- Mobile overlay --}}
                        <div
                            x-show="open"
                            x-transition:enter="transition-opacity ease-out duration-200"
                            x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100"
                            x-transition:leave="transition-opacity ease-in duration-150"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            x-on:click="open = false"
                            class="fixed inset-0 z-40 bg-zinc-900/50"
                        ></div>

                        {{-- Mobile sidebar panel --}}
                        <nav
                            x-show="open"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="-translate-x-full"
                            x-transition:enter-end="translate-x-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="translate-x-0"
                            x-transition:leave-end="-translate-x-full"
                            class="fixed inset-y-0 left-0 z-50 w-72 bg-zinc-50 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-800 overflow-y-auto"
                            x-cloak
                        >
                            {{-- Mobile header --}}
                            <div class="h-16 flex items-center justify-between px-4 border-b border-zinc-200 dark:border-zinc-800">
                                <a href="/" class="flex items-center">
                                    <img src="/images/flow-forms-logo.svg" alt="Flow Forms" class="h-8 w-auto dark:hidden" />
                                    <img src="/images/flow-forms-logo-dark.svg" alt="Flow Forms" class="h-8 w-auto hidden dark:block" />
                                </a>
                                <button
                                    type="button"
                                    class="p-2 text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200"
                                    x-on:click="open = false"
                                >
                                    <flux:icon.x-mark class="size-5" />
                                </button>
                            </div>

                            {{-- Mobile search --}}
                            <div class="p-4 border-b border-zinc-200 dark:border-zinc-800">
                                <x-docs.search />
                            </div>

                            {{-- Mobile navigation --}}
                            <div class="p-4">
                                @include('components.layouts.partials.docs-nav', ['navigationItems' => $navigationItems])
                            </div>
                        </nav>
                    </div>

                    {{-- Desktop sidebar --}}
                    <aside class="hidden lg:block w-72 shrink-0 sticky top-16 h-[calc(100vh-4rem)] overflow-y-auto border-r border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900">
                        <div class="p-6">
                            @include('components.layouts.partials.docs-nav', ['navigationItems' => $navigationItems])
                        </div>
                    </aside>

                    {{-- Main content --}}
                    <main class="flex-1 min-w-0">
                        <article class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-12">
                            {{ $slot }}
                        </article>
                    </main>
                </div>
            @else
                {{-- Full width content (no sidebar) --}}
                <main class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-12">
                    {{ $slot }}
                </main>
            @endif
        </div>

        @stack('modals')

        @fluxScripts
        @stack('scripts')
    </body>
</html>
