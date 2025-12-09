@props([
    'navigationItems' => []
])

<nav class="hidden lg:flex lg:w-64 lg:flex-col lg:bg-gray-50 lg:border-r lg:border-gray-200 dark:lg:bg-gray-800 dark:lg:border-gray-700">
    <div class="flex-1 px-4 py-6 overflow-y-auto">
        <div class="space-y-8">
            @foreach($navigationItems as $key => $item)
                @if($item['type'] === 'file')
                    {{-- Root level file --}}
                    <x-docs.nav-section>
                        <flux:navlist.item
                            href="{{ $item['url'] }}"
                            :current="request()->is($item['filename'])">
                            {{ $item['title'] }}
                        </flux:navlist.item>
                    </x-docs.nav-section>
                @elseif($item['type'] === 'folder')
                    {{-- Folder section --}}
                    <x-docs.nav-section :title="$item['title']">
                        @foreach($item['items'] as $subItem)
                            @php
                                $isActive = request()->is($subItem['folder'].'/'.$subItem['filename']);
                            @endphp
                            <flux:navlist.item
                                href="{{ $subItem['url'] }}"
                                :current="$isActive">
                                {{ $subItem['title'] }}
                            </flux:navlist.item>
                        @endforeach
                    </x-docs.nav-section>
                @endif
            @endforeach
        </div>
    </div>
</nav>