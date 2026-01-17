<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Jenssegers\Agent\Agent;
use Livewire\Component;

new class extends Component
{
    public string $password = '';

    public bool $confirmingLogout = false;

    public function getSessionsProperty(): array
    {
        if (config('session.driver') !== 'database') {
            return [];
        }

        return collect(
            DB::connection(config('session.connection'))
                ->table(config('session.table', 'sessions'))
                ->where('user_id', Auth::user()->getAuthIdentifier())
                ->orderBy('last_activity', 'desc')
                ->get()
        )->map(function ($session) {
            $agent = $this->createAgent($session);

            return (object) [
                'agent' => $agent,
                'ip_address' => $session->ip_address,
                'is_current_device' => $session->id === request()->session()->getId(),
                'last_active' => Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
            ];
        })->all();
    }

    public function confirmLogout(): void
    {
        $this->password = '';
        $this->confirmingLogout = true;
    }

    public function logoutOtherSessions(): void
    {
        $this->validate(['password' => ['required', 'string']]);

        if (! Hash::check($this->password, Auth::user()->password)) {
            throw ValidationException::withMessages([
                'password' => [__('This password does not match our records.')],
            ]);
        }

        Auth::guard('web')->logoutOtherDevices($this->password);

        $this->deleteOtherSessionRecords();

        $this->confirmingLogout = false;
        $this->password = '';

        $this->dispatch('sessions-logged-out');
    }

    protected function deleteOtherSessionRecords(): void
    {
        if (config('session.driver') !== 'database') {
            return;
        }

        DB::connection(config('session.connection'))
            ->table(config('session.table', 'sessions'))
            ->where('user_id', Auth::user()->getAuthIdentifier())
            ->where('id', '!=', request()->session()->getId())
            ->delete();
    }

    protected function createAgent($session): Agent
    {
        return tap(new Agent, fn ($agent) => $agent->setUserAgent($session->user_agent));
    }
}; ?>

<flux:card class="space-y-6">
    <div>
        <flux:heading size="lg">{{ __('Browser Sessions') }}</flux:heading>
        <flux:subheading>{{ __('Manage and log out your active sessions on other browsers and devices.') }}</flux:subheading>
    </div>

    <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">
        {{ __('If necessary, you may log out of all of your other browser sessions across all of your devices.') }}
    </flux:text>

    @if (count($this->sessions) > 0)
        <div class="space-y-4">
            @foreach ($this->sessions as $session)
                <div class="flex items-center gap-3">
                    <div class="text-zinc-500">
                        @if ($session->agent->isDesktop())
                            <flux:icon.computer-desktop class="size-8" />
                        @else
                            <flux:icon.device-phone-mobile class="size-8" />
                        @endif
                    </div>

                    <div>
                        <flux:text class="text-sm">
                            {{ $session->agent->platform() ?: __('Unknown') }} - {{ $session->agent->browser() ?: __('Unknown') }}
                        </flux:text>

                        <flux:text class="text-xs text-zinc-500">
                            {{ $session->ip_address }},
                            @if ($session->is_current_device)
                                <span class="text-green-500 font-semibold">{{ __('This device') }}</span>
                            @else
                                {{ __('Last active') }} {{ $session->last_active }}
                            @endif
                        </flux:text>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="flex items-center gap-4">
        <flux:button variant="primary" wire:click="confirmLogout">
            {{ __('Log Out Other Browser Sessions') }}
        </flux:button>

        <div x-data="{ shown: false }" x-on:sessions-logged-out.window="shown = true; setTimeout(() => shown = false, 2000)">
            <flux:text x-show="shown" x-transition class="text-sm text-green-600 dark:text-green-400">
                {{ __('Done.') }}
            </flux:text>
        </div>
    </div>

    <flux:modal wire:model="confirmingLogout" class="max-w-lg">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Log Out Other Browser Sessions') }}</flux:heading>
                <flux:text class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                    {{ __('Please enter your password to confirm.') }}
                </flux:text>
            </div>

            <flux:field>
                <flux:label>{{ __('Password') }}</flux:label>
                <flux:input wire:model="password" type="password" viewable wire:keydown.enter="logoutOtherSessions" />
                <flux:error name="password" />
            </flux:field>

            <div class="flex justify-end gap-3">
                <flux:button variant="ghost" wire:click="$set('confirmingLogout', false)">{{ __('Cancel') }}</flux:button>
                <flux:button variant="primary" wire:click="logoutOtherSessions">{{ __('Log Out Sessions') }}</flux:button>
            </div>
        </div>
    </flux:modal>
</flux:card>
