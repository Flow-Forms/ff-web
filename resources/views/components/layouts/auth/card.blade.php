<x-layouts.auth>
    <div class="flex min-h-svh flex-col items-center justify-center gap-6 bg-zinc-100 p-6 md:p-10 dark:bg-zinc-900">
        <div class="flex w-full max-w-sm flex-col gap-6">
            <a href="/" class="flex items-center justify-center">
                <x-application-logo class="h-10" />
            </a>

            <div class="flex flex-col gap-6 rounded-xl border border-zinc-200 bg-white p-10 shadow-sm dark:border-zinc-700 dark:bg-zinc-800">
                {{ $slot }}
            </div>
        </div>
    </div>
</x-layouts.auth>
