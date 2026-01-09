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
            <flux:icon.magnifying-glass class="size-5 text-gray-400" />
        </x-slot:iconLeading>
        <x-slot:suffix>
            <kbd class="hidden sm:inline-flex px-1.5 py-0.5 text-xs font-semibold text-gray-500 bg-gray-100 dark:bg-gray-700 dark:text-gray-400 rounded">⌘K</kbd>
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
        class="absolute z-50 mt-2 w-full max-w-2xl bg-white dark:bg-gray-800 rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 dark:ring-gray-700 overflow-hidden"
        x-on:keydown.down.prevent="navigateDown"
        x-on:keydown.up.prevent="navigateUp"
        x-on:keydown.enter.prevent="selectResult"
    >
        <div x-show="loading" class="p-4 text-center text-gray-500 dark:text-gray-400">
            <flux:icon.arrow-path class="animate-spin size-5 mx-auto mb-2" />
            <p class="text-sm">Searching...</p>
        </div>

        <div x-show="!loading && query.length > 0 && results.length === 0" class="p-8 text-center">
            <flux:icon.magnifying-glass class="size-8 mx-auto mb-2 text-gray-400" />
            <p class="text-gray-500 dark:text-gray-400">No results found for "<span x-text="query" class="font-semibold"></span>"</p>
            <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Try searching for something else</p>
        </div>

        <div x-show="!loading && results.length > 0" class="max-h-96 overflow-y-auto">
            <template x-for="(group, groupIndex) in results" :key="groupIndex">
                <div>
                    <div class="px-4 py-2 bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider" x-text="group.section"></h3>
                    </div>

                    <template x-for="(result, resultIndex) in group.items" :key="result.id">
                        <a
                            :href="result.url"
                            x-on:mouseenter="selectedIndex = getGlobalIndex(groupIndex, resultIndex)"
                            :class="{
                                'bg-indigo-50 dark:bg-indigo-900/20': selectedIndex === getGlobalIndex(groupIndex, resultIndex),
                                'hover:bg-gray-50 dark:hover:bg-gray-700/50': selectedIndex !== getGlobalIndex(groupIndex, resultIndex)
                            }"
                            class="block px-4 py-3 border-b border-gray-100 dark:border-gray-700 last:border-0 transition-colors"
                        >
                            <div class="flex items-center justify-between mb-1">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100" x-html="highlightMatch(result.title)"></h4>
                                <flux:icon.arrow-right class="size-4 text-gray-400" x-show="selectedIndex === getGlobalIndex(groupIndex, resultIndex)" />
                            </div>

                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1" x-text="result.breadcrumb"></p>

                            <p class="text-xs text-gray-600 dark:text-gray-300 line-clamp-2" x-html="highlightMatch(result.content)"></p>
                        </a>
                    </template>
                </div>
            </template>
        </div>

        <div x-show="!loading && results.length > 0" class="px-4 py-2 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                <div class="flex items-center gap-4">
                    <span class="flex items-center gap-1">
                        <kbd class="px-1.5 py-0.5 font-semibold bg-gray-100 dark:bg-gray-700 rounded">↓</kbd>
                        <kbd class="px-1.5 py-0.5 font-semibold bg-gray-100 dark:bg-gray-700 rounded">↑</kbd>
                        Navigate
                    </span>
                    <span class="flex items-center gap-1">
                        <kbd class="px-1.5 py-0.5 font-semibold bg-gray-100 dark:bg-gray-700 rounded">↵</kbd>
                        Open
                    </span>
                    <span class="flex items-center gap-1">
                        <kbd class="px-1.5 py-0.5 font-semibold bg-gray-100 dark:bg-gray-700 rounded">esc</kbd>
                        Close
                    </span>
                </div>
                <span><span x-text="results.length"></span> results</span>
            </div>
        </div>
    </div>
</div>
