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
                @foreach($navigationItems as $key => $item)
                    @if($item['type'] === 'file')
                        {{-- Root level file --}}
                        <x-docs.nav-section>
                            <flux:navlist.item
                                href="{{ $item['url'] }}"
                                :current="request()->is($item['filename'])"
                                x-on:click="open = false">
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
                                    :current="$isActive"
                                    x-on:click="open = false">
                                    {{ $subItem['title'] }}
                                </flux:navlist.item>
                            @endforeach
                        </x-docs.nav-section>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</div>