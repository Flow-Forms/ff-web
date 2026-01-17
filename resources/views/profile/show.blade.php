<x-layouts.app :showSidebar="false" title="Profile Settings">
    <div class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <flux:heading size="xl">{{ __('Profile Settings') }}</flux:heading>
            <flux:subheading>{{ __('Manage your account settings and preferences.') }}</flux:subheading>
        </div>

        <div class="space-y-8">
            @if (Laravel\Fortify\Features::canUpdateProfileInformation())
                <livewire:profile.update-profile-information-form />
            @endif

            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
                <flux:separator />
                <livewire:profile.update-password-form />
            @endif

            @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                <flux:separator />
                <livewire:profile.two-factor-authentication-form />
            @endif

            <flux:separator />
            <livewire:profile.logout-other-browser-sessions-form />

            @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
                <flux:separator />
                <livewire:profile.delete-user-form />
            @endif
        </div>
    </div>
</x-layouts.app>
