<nav class="space-y-8">
    @foreach($navigationItems as $key => $item)
        @if($item['type'] === 'file')
            {{-- Root level item --}}
            <a
                href="{{ $item['url'] }}"
                x-on:click="$dispatch('close-sidebar')"
                @class([
                    'block text-sm font-medium transition-colors',
                    'text-zinc-900 dark:text-white' => request()->is($item['filename']),
                    'text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white' => !request()->is($item['filename']),
                ])
            >
                {{ $item['title'] }}
            </a>
        @elseif($item['type'] === 'folder')
            {{-- Section with items --}}
            <div>
                <h3 class="text-sm font-semibold text-zinc-900 dark:text-white mb-3">
                    {{ $item['title'] }}
                </h3>
                <ul class="space-y-2 border-l border-zinc-200 dark:border-zinc-800">
                    @foreach($item['items'] as $subItem)
                        @php
                            $isActive = request()->is($subItem['folder'].'/'.$subItem['filename']);
                        @endphp
                        <li>
                            <a
                                href="{{ $subItem['url'] }}"
                                x-on:click="$dispatch('close-sidebar')"
                                @class([
                                    'block text-sm pl-4 -ml-px border-l transition-colors',
                                    'border-cyan-500 text-cyan-600 dark:text-cyan-400 font-medium' => $isActive,
                                    'border-transparent text-zinc-600 hover:text-zinc-900 hover:border-zinc-300 dark:text-zinc-400 dark:hover:text-white dark:hover:border-zinc-600' => !$isActive,
                                ])
                            >
                                {{ $subItem['title'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    @endforeach
</nav>
