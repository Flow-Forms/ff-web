@props([
    'title',
    'description' => '',
])

<div class="flex flex-col space-y-1">
    <flux:heading size="lg">{{ $title }}</flux:heading>
    @if ($description)
        <flux:subheading>{{ $description }}</flux:subheading>
    @endif
</div>
