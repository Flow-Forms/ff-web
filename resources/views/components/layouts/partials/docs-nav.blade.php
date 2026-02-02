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
                        @if($subItem['type'] === 'file')
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
                        @elseif($subItem['type'] === 'subfolder')
                            <li>
                                <span class="block text-sm pl-4 -ml-px border-l border-transparent font-medium text-zinc-800 dark:text-zinc-200 mt-3 mb-1">
                                    {{ $subItem['title'] }}
                                </span>
                                <ul class="space-y-2">
                                    @foreach($subItem['items'] as $nestedItem)
                                        @php
                                            $isNestedActive = request()->is($nestedItem['folder'].'/'.$nestedItem['subfolder'].'/'.$nestedItem['filename']);
                                        @endphp
                                        <li>
                                            <a
                                                href="{{ $nestedItem['url'] }}"
                                                x-on:click="$dispatch('close-sidebar')"
                                                @class([
                                                    'block text-sm pl-8 -ml-px border-l transition-colors',
                                                    'border-cyan-500 text-cyan-600 dark:text-cyan-400 font-medium' => $isNestedActive,
                                                    'border-transparent text-zinc-600 hover:text-zinc-900 hover:border-zinc-300 dark:text-zinc-400 dark:hover:text-white dark:hover:border-zinc-600' => !$isNestedActive,
                                                ])
                                            >
                                                {{ $nestedItem['title'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        @endif
    @endforeach
</nav>
