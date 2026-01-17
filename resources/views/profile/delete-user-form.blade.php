<flux:card class="space-y-6">
    <div>
        <flux:heading size="lg">{{ __('Delete Account') }}</flux:heading>
        <flux:subheading>{{ __('Permanently delete your account.') }}</flux:subheading>
    </div>

    <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">
        {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
    </flux:text>

    <div>
        <flux:button variant="danger" wire:click="confirmUserDeletion" wire:loading.attr="disabled">
            {{ __('Delete Account') }}
        </flux:button>
    </div>

    <flux:modal wire:model.live="confirmingUserDeletion" class="max-w-lg">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Delete Account') }}</flux:heading>
                <flux:text class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                    {{ __('Are you sure you want to delete your account? Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                </flux:text>
            </div>

            <flux:field
                x-data="{}"
                x-on:confirming-delete-user.window="setTimeout(() => $refs.password.focus(), 250)"
            >
                <flux:label>{{ __('Password') }}</flux:label>
                <flux:input
                    x-ref="password"
                    wire:model="password"
                    type="password"
                    autocomplete="current-password"
                    placeholder="{{ __('Password') }}"
                    wire:keydown.enter="deleteUser"
                    viewable
                />
                @error('password')
                    <flux:error>{{ $message }}</flux:error>
                @enderror
            </flux:field>

            <div class="flex justify-end gap-3">
                <flux:button variant="ghost" wire:click="$toggle('confirmingUserDeletion')" wire:loading.attr="disabled">
                    {{ __('Cancel') }}
                </flux:button>

                <flux:button variant="danger" wire:click="deleteUser" wire:loading.attr="disabled">
                    {{ __('Delete Account') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
</flux:card>
