@props([
    'navigationItems' => []
])

<div class="lg:hidden" x-data="{ open: false }">
    <!-- Mobile menu button -->
    <div class="fixed top-4 left-4 z-50">
        <flux:button x-on:click="open = !open" size="sm" variant="outline" class="shadow-md">
            <flux:icon.bars-3 class="h-5 w-5" />
        </flux:button>
    </div>
    
    <!-- Mobile menu overlay -->
    <div x-show="open" x-on:click="open = false" class="fixed inset-0 z-40 bg-black bg-opacity-25"></div>
    
    <!-- Mobile sidebar -->
    <div x-show="open" class="fixed top-0 left-0 z-50 w-64 h-full bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 overflow-y-auto">
        <div class="p-4">
            <div class="flex items-center justify-between mb-6">
                <flux:heading size="base">Navigation</flux:heading>
                <div class="flex items-center gap-2">
                    <x-docs.theme-toggle />
                    <flux:button x-on:click="open = false" size="sm" variant="ghost">
                        <flux:icon.x-mark class="h-5 w-5" />
                    </flux:button>
                </div>
            </div>
            
            {{-- Mobile Search --}}
            <div class="mb-6">
                <x-docs.search />
            </div>
            
            <!-- Navigation content -->
            <div class="space-y-6">
                {{-- Root level files --}}
                @if(!empty($navigationItems['_root']))
                    <x-docs.nav-section>
                        @foreach($navigationItems['_root'] as $item)
                            <flux:navlist.item
                                href="{{ $item['url'] }}"
                                :current="request()->is($item['filename'])"
                                x-on:click="open = false">
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
                                    :current="$isActive"
                                    x-on:click="open = false">
                                    {{ $item['title'] }}
                                </flux:navlist.item>
                            @endforeach
                        </x-docs.nav-section>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</div>