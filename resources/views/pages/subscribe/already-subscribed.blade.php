<?php
use function Laravel\Folio\name;

name('subscribe.already-subscribed');
?>

<x-layouts.landing title="Already Subscribed - Flow Forms">
    <flux:heading size="xl" class="mb-4 text-gray-900 dark:text-white">
        You're already on the list!
    </flux:heading>
    <flux:text class="text-gray-600 dark:text-gray-400">
        Looks like you've already subscribed. No action needed.
    </flux:text>
</x-layouts.landing>
