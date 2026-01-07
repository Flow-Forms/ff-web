<?php
use function Laravel\Folio\name;

name('subscribe.subscribed');
?>

<x-layouts.landing title="Subscribed - Flow Forms">
    <flux:heading size="xl" class="mb-4 text-gray-900 dark:text-white">
        Welcome aboard!
    </flux:heading>
    <flux:text class="text-gray-600 dark:text-gray-400">
        You're now subscribed and will receive updates from us.
    </flux:text>
</x-layouts.landing>
