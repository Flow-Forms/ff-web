<flux:navlist>
    @foreach($navigationItems as $key => $item)
        @if($item['type'] === 'file')
            <flux:navlist.item
                href="{{ $item['url'] }}"
                :current="request()->is($item['filename'])"
                x-on:click="$dispatch('close-sidebar')"
            >
                {{ $item['title'] }}
            </flux:navlist.item>

        @elseif($item['type'] === 'folder')
            <flux:navlist.group heading="{{ $item['title'] }}">
                <div class="relative ps-7 space-y-[2px]">
                    {{-- Vertical line indicator --}}
                    <div class="absolute inset-y-[3px] w-px bg-zinc-200 dark:bg-white/30 start-0 ms-4"></div>

                    @foreach($item['items'] as $subItem)
                        @if($subItem['type'] === 'file')
                            <flux:navlist.item
                                href="{{ $subItem['url'] }}"
                                :current="request()->is($subItem['folder'] . '/' . $subItem['filename'])"
                                x-on:click="$dispatch('close-sidebar')"
                            >
                                {{ $subItem['title'] }}
                            </flux:navlist.item>

                        @elseif($subItem['type'] === 'subfolder')
                            @php
                                $subfolderOwnUrl = $subItem['url'] ?? null;
                                $isOwnPageActive = $subfolderOwnUrl && request()->is(trim($subfolderOwnUrl, '/'));
                                $isChildActive = collect($subItem['items'])->contains(function ($leaf) {
                                    $urlPath = trim($leaf['url'], '/');
                                    return request()->is($urlPath);
                                });
                                $isSubfolderActive = $isOwnPageActive || $isChildActive;
                            @endphp

                            @if($subfolderOwnUrl)
                                {{-- Subfolder with its own content page: heading is a navigable link --}}
                                <div x-data="{ open: @js($isSubfolderActive) }" class="group/linkable-disclosure">
                                    <div class="flex items-center h-10 lg:h-8 mb-[2px] rounded-lg hover:bg-zinc-800/5 dark:hover:bg-white/[7%]">
                                        <button @click.stop="open = !open" type="button" class="ps-3 pe-1 shrink-0 text-zinc-500 dark:text-white/80">
                                            <flux:icon.chevron-down x-show="open" x-cloak class="size-3!" />
                                            <flux:icon.chevron-right x-show="!open" class="size-3!" />
                                        </button>
                                        <a
                                            href="{{ $subfolderOwnUrl }}"
                                            class="flex-1 text-sm font-medium leading-none pe-3 {{ $isOwnPageActive ? 'text-zinc-800 dark:text-white' : 'text-zinc-500 hover:text-zinc-800 dark:text-white/80 dark:hover:text-white' }}"
                                            x-on:click="$dispatch('close-sidebar')"
                                        >
                                            {{ $subItem['title'] }}
                                        </a>
                                    </div>

                                    <div x-show="open" x-cloak class="relative space-y-[2px] ps-7">
                                        <div class="absolute inset-y-[3px] w-px bg-zinc-200 dark:bg-white/30 start-0 ms-4"></div>

                                        @foreach($subItem['items'] as $leafItem)
                                            @php
                                                $leafUrlPath = trim($leafItem['url'], '/');
                                                $isLeafActive = request()->is($leafUrlPath);
                                            @endphp
                                            <flux:navlist.item
                                                href="{{ $leafItem['url'] }}"
                                                :current="$isLeafActive"
                                                x-on:click="$dispatch('close-sidebar')"
                                            >
                                                {{ $leafItem['title'] }}
                                            </flux:navlist.item>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                {{-- Regular subfolder: heading is a toggle only --}}
                                <flux:navlist.group
                                    heading="{{ $subItem['title'] }}"
                                    :expandable="true"
                                    :expanded="$isSubfolderActive"
                                >
                                    @foreach($subItem['items'] as $leafItem)
                                        @php
                                            $leafUrlPath = trim($leafItem['url'], '/');
                                            $isLeafActive = request()->is($leafUrlPath);
                                        @endphp
                                        <flux:navlist.item
                                            href="{{ $leafItem['url'] }}"
                                            :current="$isLeafActive"
                                            x-on:click="$dispatch('close-sidebar')"
                                        >
                                            {{ $leafItem['title'] }}
                                        </flux:navlist.item>
                                    @endforeach
                                </flux:navlist.group>
                            @endif
                        @endif
                    @endforeach
                </div>
            </flux:navlist.group>
        @endif
    @endforeach
</flux:navlist>
