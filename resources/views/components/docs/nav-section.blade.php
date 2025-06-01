@props([
    'title' => null,
    'items' => []
])

<div>
    @if($title)
        <flux:subheading class="mb-3">{{ $title }}</flux:subheading>
    @endif
    <flux:navlist>
        {{ $slot }}
    </flux:navlist>
</div>