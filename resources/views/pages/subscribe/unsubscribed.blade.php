<?php
use function Laravel\Folio\name;

name('subscribe.unsubscribed');
?>

<x-layouts.landing title="Unsubscribed - Flow Forms">
    <flux:heading size="xl" class="mb-4 text-gray-900 dark:text-white">
        Sorry to see you go
    </flux:heading>
    <flux:text class="text-gray-600 dark:text-gray-400">
        You've been unsubscribed and won't receive further emails from us.
    </flux:text>
</x-layouts.landing>
