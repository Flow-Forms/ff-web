@props([
    'navigationItems' => []
])

<nav class="hidden lg:flex lg:w-64 lg:flex-col lg:bg-gray-50 lg:border-r lg:border-gray-200 dark:lg:bg-gray-800 dark:lg:border-gray-700">
    <div class="flex-1 px-4 py-6 overflow-y-auto">
        <div class="space-y-8">
            {{-- Root level files --}}
            @if(!empty($navigationItems['_root']))
                <x-docs.nav-section>
                    @foreach($navigationItems['_root'] as $item)
                        <flux:navlist.item
                            href="{{ $item['url'] }}"
                            :current="request()->is($item['filename'])">
                            {{ $item['title'] }}
                        </flux:navlist.item>
                    @endforeach
                </x-docs.nav-section>
            @endif

            {{-- Folder sections --}}
            @foreach($navigationItems as $key => $section)
                @if($key !== '_root' && isset($section['type']) && $section['type'] === 'folder')
                    <x-docs.nav-section :title="$section['title']">
                        @foreach($section['items'] as $item)
                            @php
                                $isActive = request()->is($item['folder'].'/'.$item['filename']);
                            @endphp
                            <flux:navlist.item
                                href="{{ $item['url'] }}"
                                :current="$isActive">
                                {{ $item['title'] }}
                            </flux:navlist.item>
                        @endforeach
                    </x-docs.nav-section>
                @endif
            @endforeach
        </div>
    </div>
</nav>