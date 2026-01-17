<flux:card class="space-y-6">
    <div>
        <flux:heading size="lg">{{ __('Update Password') }}</flux:heading>
        <flux:subheading>{{ __('Ensure your account is using a long, random password to stay secure.') }}</flux:subheading>
    </div>

    <form wire:submit="updatePassword" class="space-y-6">
        <flux:field>
            <flux:label>{{ __('Current Password') }}</flux:label>
            <flux:input
                wire:model="state.current_password"
                type="password"
                autocomplete="current-password"
                viewable
            />
            @error('current_password')
                <flux:error>{{ $message }}</flux:error>
            @enderror
        </flux:field>

        <flux:field>
            <flux:label>{{ __('New Password') }}</flux:label>
            <flux:input
                wire:model="state.password"
                type="password"
                autocomplete="new-password"
                viewable
            />
            @error('password')
                <flux:error>{{ $message }}</flux:error>
            @enderror
        </flux:field>

        <flux:field>
            <flux:label>{{ __('Confirm Password') }}</flux:label>
            <flux:input
                wire:model="state.password_confirmation"
                type="password"
                autocomplete="new-password"
                viewable
            />
            @error('password_confirmation')
                <flux:error>{{ $message }}</flux:error>
            @enderror
        </flux:field>

        <div class="flex items-center gap-4">
            <flux:button type="submit" variant="primary">
                {{ __('Save') }}
            </flux:button>

            <x-action-message on="saved">
                <flux:text class="text-sm text-green-600 dark:text-green-400">{{ __('Saved.') }}</flux:text>
            </x-action-message>
        </div>
    </form>
</flux:card>
