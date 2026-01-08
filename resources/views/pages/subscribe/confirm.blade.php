<?php
use function Laravel\Folio\name;

name('subscribe.confirm');
?>

<x-layouts.landing title="Confirm Subscription - Flow Forms">
    <flux:heading size="xl" class="mb-4 text-gray-900 dark:text-white">
        One more step!
    </flux:heading>
    <flux:text class="text-gray-600 dark:text-gray-400">
        Check your inbox for a confirmation email to complete your subscription.
    </flux:text>
</x-layouts.landing>
