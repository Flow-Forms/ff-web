@props([
    'placeholder' => 'Search documentation...',
    'class' => ''
])

<div
    x-data="docsSearch()"
    x-on:keydown.window.cmd.k.prevent="$refs.searchInput.focus()"
    x-on:keydown.window.ctrl.k.prevent="$refs.searchInput.focus()"
    x-on:click.outside="showResults = false"
    class="relative {{ $class }}"
>
    <flux:input
        x-ref="searchInput"
        x-model="query"
        x-on:input.debounce.300ms="search"
        x-on:focus="onFocus"
        x-on:keydown.down.prevent="navigateDown"
        x-on:keydown.up.prevent="navigateUp"
        x-on:keydown.enter.prevent="selectResult"
        x-on:keydown.escape="showResults = false"
        type="search"
        :placeholder="$placeholder"
        class="w-full"
    >
        <x-slot:iconLeading>
            <flux:icon.magnifying-glass class="size-5 text-zinc-400" />
        </x-slot:iconLeading>
        <x-slot:suffix>
            <kbd class="hidden sm:inline-flex px-1.5 py-0.5 text-xs font-semibold text-zinc-500 bg-zinc-100 dark:bg-white/10 dark:text-zinc-300 rounded">⌘K</kbd>
        </x-slot:suffix>
    </flux:input>

    {{-- Search Results Dropdown --}}
    <div
        x-show="showResults && (results.length > 0 || (query.length > 0 && !loading))"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute z-50 mt-2 w-full max-w-2xl bg-white dark:bg-zinc-700 rounded-xl shadow-lg border border-zinc-200 dark:border-zinc-600 overflow-hidden"
        x-on:keydown.down.prevent="navigateDown"
        x-on:keydown.up.prevent="navigateUp"
        x-on:keydown.enter.prevent="selectResult"
    >
        <div x-show="loading" class="p-4 text-center text-zinc-500 dark:text-zinc-400">
            <flux:icon.arrow-path class="animate-spin size-5 mx-auto mb-2" />
            <p class="text-sm">Searching...</p>
        </div>

        <div x-show="!loading && query.length > 0 && results.length === 0" class="p-8 text-center">
            <flux:icon.magnifying-glass class="size-8 mx-auto mb-2 text-zinc-400" />
            <p class="text-zinc-500 dark:text-zinc-400">No results found for "<span x-text="query" class="font-semibold"></span>"</p>
            <p class="text-sm text-zinc-400 dark:text-zinc-500 mt-1">Try searching for something else</p>
        </div>

        <div x-show="!loading && results.length > 0" class="max-h-96 overflow-y-auto">
            <template x-for="(group, groupIndex) in results" :key="groupIndex">
                <div>
                    <div class="px-4 py-2 bg-zinc-50 dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-600">
                        <h3 class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider" x-text="group.section"></h3>
                    </div>

                    <template x-for="(result, resultIndex) in group.items" :key="result.id">
                        <a
                            :href="result.url"
                            x-on:mouseenter="selectedIndex = getGlobalIndex(groupIndex, resultIndex)"
                            :class="{
                                'bg-zinc-100 dark:bg-zinc-600': selectedIndex === getGlobalIndex(groupIndex, resultIndex),
                            }"
                            class="block px-4 py-3 border-b border-zinc-100 dark:border-zinc-600 last:border-0 rounded-md transition-colors"
                        >
                            <div class="flex items-center justify-between mb-1">
                                <h4 class="text-sm font-medium text-zinc-800 dark:text-white" x-html="highlightMatch(result.title)"></h4>
                                <flux:icon.arrow-right class="size-4 text-zinc-400" x-show="selectedIndex === getGlobalIndex(groupIndex, resultIndex)" />
                            </div>

                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-1" x-text="result.breadcrumb"></p>

                            <p class="text-xs text-zinc-600 dark:text-zinc-300 line-clamp-2" x-html="highlightMatch(result.content)"></p>
                        </a>
                    </template>
                </div>
            </template>
        </div>

        <div x-show="!loading && results.length > 0" class="px-4 py-2 bg-zinc-50 dark:bg-zinc-800 border-t border-zinc-200 dark:border-zinc-600">
            <div class="flex items-center justify-between text-xs text-zinc-500 dark:text-zinc-400">
                <div class="flex items-center gap-4">
                    <span class="flex items-center gap-1">
                        <kbd class="px-1.5 py-0.5 font-semibold bg-zinc-100 dark:bg-white/10 rounded">↓</kbd>
                        <kbd class="px-1.5 py-0.5 font-semibold bg-zinc-100 dark:bg-white/10 rounded">↑</kbd>
                        Navigate
                    </span>
                    <span class="flex items-center gap-1">
                        <kbd class="px-1.5 py-0.5 font-semibold bg-zinc-100 dark:bg-white/10 rounded">↵</kbd>
                        Open
                    </span>
                    <span class="flex items-center gap-1">
                        <kbd class="px-1.5 py-0.5 font-semibold bg-zinc-100 dark:bg-white/10 rounded">esc</kbd>
                        Close
                    </span>
                </div>
                <span><span x-text="results.length"></span> results</span>
            </div>
        </div>
    </div>
</div>
