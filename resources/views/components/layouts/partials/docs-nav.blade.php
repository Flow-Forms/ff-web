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
                            $isSubfolderActive = collect($subItem['items'])->contains(function ($leaf) {
                                $urlPath = trim($leaf['url'], '/');
                                return request()->is($urlPath);
                            });
                        @endphp
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
                @endforeach
            </flux:navlist.group>
        @endif
    @endforeach
</flux:navlist>
