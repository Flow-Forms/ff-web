<?php

use function Laravel\Folio\{middleware, name};

middleware(['auth', 'verified']);
name('settings');

?>

<x-layouts.app :showSidebar="false" title="Settings">
    <div class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <flux:heading size="xl">{{ __('Settings') }}</flux:heading>
            <flux:subheading>{{ __('Manage your account settings and preferences.') }}</flux:subheading>
        </div>

        <div class="space-y-8">
            <livewire:settings.profile />

            <flux:separator />

            <livewire:settings.password />

            @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                <flux:separator />
                <livewire:settings.two-factor />
            @endif

            <flux:separator />

            <livewire:settings.sessions />

            <flux:separator />

            <livewire:settings.delete-account />
        </div>
    </div>
</x-layouts.app>
