@props([
    'title' => config('app.name', 'Flow Forms')
])

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

    <body class="font-sans antialiased bg-gray-50 dark:bg-zinc-900">
        <div class="min-h-screen flex items-center justify-center p-4">
            <div class="w-full max-w-md">
                <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-lg p-12 text-center">
                    <a href="/" class="inline-block mb-8">
                        <img src="{{ asset('images/flow-forms-logo.png') }}" alt="Flow Forms" class="h-16 w-auto mx-auto" />
                    </a>

                    {{ $slot }}

                    <div class="mt-8">
                        <flux:link href="/" class="inline-flex items-center gap-1 text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300">
                            View Documentation
                            <flux:icon.arrow-right class="size-4" />
                        </flux:link>
                    </div>
                </div>
            </div>
        </div>

        @fluxScripts
    </body>
</html>
