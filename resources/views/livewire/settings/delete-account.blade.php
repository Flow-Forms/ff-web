<?php

use App\Actions\DeleteUser;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    public string $password = '';

    public bool $confirmingDeletion = false;

    public function confirmDeletion(): void
    {
        $this->password = '';
        $this->confirmingDeletion = true;
    }

    public function deleteAccount(DeleteUser $deleter): void
    {
        $deleter->delete(Auth::user(), $this->password);

        Auth::guard('web')->logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        $this->redirect('/', navigate: true);
    }
}; ?>

<flux:card class="space-y-6">
    <div>
        <flux:heading size="lg">{{ __('Delete Account') }}</flux:heading>
        <flux:subheading>{{ __('Permanently delete your account.') }}</flux:subheading>
    </div>

    <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">
        {{ __('Once your account is deleted, all of its resources and data will be permanently deleted.') }}
    </flux:text>

    <div>
        <flux:button variant="danger" wire:click="confirmDeletion">
            {{ __('Delete Account') }}
        </flux:button>
    </div>

    <flux:modal wire:model="confirmingDeletion" class="max-w-lg">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Delete Account') }}</flux:heading>
                <flux:text class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                    {{ __('Are you sure? This action cannot be undone. Please enter your password to confirm.') }}
                </flux:text>
            </div>

            <flux:field>
                <flux:label>{{ __('Password') }}</flux:label>
                <flux:input wire:model="password" type="password" viewable wire:keydown.enter="deleteAccount" />
                <flux:error name="password" />
            </flux:field>

            <div class="flex justify-end gap-3">
                <flux:button variant="ghost" wire:click="$set('confirmingDeletion', false)">{{ __('Cancel') }}</flux:button>
                <flux:button variant="danger" wire:click="deleteAccount">{{ __('Delete Account') }}</flux:button>
            </div>
        </div>
    </flux:modal>
</flux:card>
