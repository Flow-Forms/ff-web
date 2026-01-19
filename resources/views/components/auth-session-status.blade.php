@props(['status'])

@if ($status)
    <flux:callout variant="success" icon="check-circle">
        {{ $status }}
    </flux:callout>
@endif
