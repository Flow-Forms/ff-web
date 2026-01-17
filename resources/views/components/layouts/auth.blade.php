<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Flow Forms') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @fluxAppearance

        <!-- Fathom - beautiful, simple website analytics -->
        <script src="https://cdn.usefathom.com/script.js" data-site="KHJGOLPI" defer></script>
    </head>
    <body class="font-sans antialiased bg-zinc-50 dark:bg-zinc-900">
        <div class="min-h-screen flex flex-col">
            {{-- Header --}}
            <header class="sticky top-0 z-50 h-16 flex items-center border-b border-zinc-200 dark:border-zinc-800 bg-white/95 dark:bg-zinc-900/95 backdrop-blur supports-[backdrop-filter]:bg-white/80 dark:supports-[backdrop-filter]:bg-zinc-900/80">
                <div class="w-full max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center gap-4">
                    {{-- Logo --}}
                    <a href="/" class="flex items-center shrink-0">
                        <img src="/images/flow-forms-logo.svg" alt="Flow Forms" class="h-9 w-auto dark:hidden" />
                        <img src="/images/flow-forms-logo-dark.svg" alt="Flow Forms" class="h-9 w-auto hidden dark:block" />
                    </a>

                    {{-- Section switcher --}}
                    <nav class="hidden sm:flex items-center gap-1 p-1 bg-zinc-100 dark:bg-zinc-800 rounded-lg">
                        <a href="/" class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100">
                            Docs
                        </a>
                        <a href="{{ route('dashboard') }}" class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100">
                            Video
                        </a>
                    </nav>

                    {{-- Spacer --}}
                    <div class="flex-1"></div>

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
                    </div>
                </div>
            </header>

            {{-- Main content --}}
            <main class="flex-1 flex items-center justify-center p-6 sm:p-8">
                {{ $slot }}
            </main>

            {{-- Footer --}}
            <footer class="py-6 text-center">
                <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">
                    &copy; {{ date('Y') }} Flow Forms. All rights reserved.
                </flux:text>
            </footer>
        </div>

        @fluxScripts
    </body>
</html>
