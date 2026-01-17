# Remove Jetstream, Use Livewire 4 SFC Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Remove Jetstream dependency and simplify to Livewire 4 single-file components (SFC) for profile management, keeping Fortify for auth backend.

**Architecture:** Replace Jetstream's Livewire components with Livewire 4 SFC components in Folio pages. Fortify handles all auth logic. Sanctum continues to provide API tokens directly (without Jetstream wrapper).

**Tech Stack:** Laravel 12, Livewire 4 SFC, Fortify, Sanctum, Flux UI, Folio

---

## Task 1: Add Fortify as Direct Dependency

**Files:**
- Modify: `composer.json`

**Step 1: Update composer.json**

Run:
```bash
composer require laravel/fortify --no-interaction
```

This ensures Fortify is a direct dependency, not just pulled in via Jetstream.

**Step 2: Verify Fortify is direct dependency**

Run: `composer show laravel/fortify`
Expected: Shows Fortify package info

**Step 3: Commit**

```bash
git add composer.json composer.lock
git commit -m "chore: add laravel/fortify as direct dependency"
```

---

## Task 2: Update CreateNewUser Action

**Files:**
- Modify: `app/Actions/Fortify/CreateNewUser.php`

**Step 1: Remove Jetstream reference**

Replace the entire file with:

```php
<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
        ])->validate();

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);
    }
}
```

**Step 2: Run Pint**

Run: `vendor/bin/pint app/Actions/Fortify/CreateNewUser.php`

**Step 3: Commit**

```bash
git add app/Actions/Fortify/CreateNewUser.php
git commit -m "refactor: remove Jetstream reference from CreateNewUser"
```

---

## Task 3: Create DeleteUser Action (Without Jetstream)

**Files:**
- Create: `app/Actions/DeleteUser.php`
- Delete: `app/Actions/Jetstream/DeleteUser.php`

**Step 1: Create new DeleteUser action**

Create `app/Actions/DeleteUser.php`:

```php
<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class DeleteUser
{
    /**
     * Delete the given user after validating password.
     */
    public function delete(User $user, string $password): void
    {
        if (! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => [__('This password does not match our records.')],
            ]);
        }

        $user->tokens->each->delete();
        $user->delete();
    }
}
```

**Step 2: Delete old Jetstream action**

Run: `rm app/Actions/Jetstream/DeleteUser.php && rmdir app/Actions/Jetstream`

**Step 3: Run Pint**

Run: `vendor/bin/pint app/Actions/DeleteUser.php`

**Step 4: Commit**

```bash
git add app/Actions/DeleteUser.php
git rm app/Actions/Jetstream/DeleteUser.php
git commit -m "refactor: move DeleteUser action out of Jetstream namespace"
```

---

## Task 4: Remove JetstreamServiceProvider

**Files:**
- Delete: `app/Providers/JetstreamServiceProvider.php`
- Modify: `bootstrap/providers.php`

**Step 1: Remove from providers array**

Update `bootstrap/providers.php`:

```php
<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\FolioServiceProvider::class,
    App\Providers\FortifyServiceProvider::class,
];
```

**Step 2: Delete the provider file**

Run: `rm app/Providers/JetstreamServiceProvider.php`

**Step 3: Commit**

```bash
git rm app/Providers/JetstreamServiceProvider.php
git add bootstrap/providers.php
git commit -m "refactor: remove JetstreamServiceProvider"
```

---

## Task 5: Create Settings Folio Page with SFC

**Files:**
- Create: `resources/views/pages/settings/index.blade.php`

**Step 1: Create settings index page**

Create `resources/views/pages/settings/index.blade.php`:

```php
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
```

**Step 2: Commit**

```bash
git add resources/views/pages/settings/index.blade.php
git commit -m "feat: add settings Folio page"
```

---

## Task 6: Create Profile SFC Component

**Files:**
- Create: `resources/views/livewire/settings/profile.blade.php`

**Step 1: Create profile SFC**

Create `resources/views/livewire/settings/profile.blade.php`:

```php
<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component {
    public string $name = '';
    public string $email = '';

    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    public function updateProfile(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore(Auth::id())],
        ]);

        $user = Auth::user();
        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated');
    }

    public function resendVerification(): void
    {
        Auth::user()->sendEmailVerificationNotification();
        $this->dispatch('verification-sent');
    }
}; ?>

<flux:card class="space-y-6">
    <div>
        <flux:heading size="lg">{{ __('Profile Information') }}</flux:heading>
        <flux:subheading>{{ __("Update your account's profile information and email address.") }}</flux:subheading>
    </div>

    <form wire:submit="updateProfile" class="space-y-6">
        <flux:field>
            <flux:label>{{ __('Name') }}</flux:label>
            <flux:input wire:model="name" type="text" required autocomplete="name" />
            <flux:error name="name" />
        </flux:field>

        <flux:field>
            <flux:label>{{ __('Email') }}</flux:label>
            <flux:input wire:model="email" type="email" required autocomplete="email" />
            <flux:error name="email" />

            @if (Auth::user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! Auth::user()->hasVerifiedEmail())
                <div class="mt-2">
                    <flux:text class="text-sm">
                        {{ __('Your email address is unverified.') }}
                        <flux:link variant="subtle" wire:click.prevent="resendVerification" class="cursor-pointer">
                            {{ __('Click here to re-send the verification email.') }}
                        </flux:link>
                    </flux:text>

                    @if (session('verification-sent'))
                        <flux:text class="mt-2 text-sm text-green-600 dark:text-green-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </flux:text>
                    @endif
                </div>
            @endif
        </flux:field>

        <div class="flex items-center gap-4">
            <flux:button type="submit" variant="primary">{{ __('Save') }}</flux:button>

            <div x-data="{ shown: false }" x-on:profile-updated.window="shown = true; setTimeout(() => shown = false, 2000)">
                <flux:text x-show="shown" x-transition class="text-sm text-green-600 dark:text-green-400">
                    {{ __('Saved.') }}
                </flux:text>
            </div>
        </div>
    </form>
</flux:card>
```

**Step 2: Commit**

```bash
git add resources/views/livewire/settings/profile.blade.php
git commit -m "feat: add profile SFC component"
```

---

## Task 7: Create Password SFC Component

**Files:**
- Create: `resources/views/livewire/settings/password.blade.php`

**Step 1: Create password SFC**

Create `resources/views/livewire/settings/password.blade.php`:

```php
<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component {
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function updatePassword(): void
    {
        $validated = $this->validate([
            'current_password' => ['required', 'string', 'current_password'],
            'password' => ['required', 'string', Password::defaults(), 'confirmed'],
        ]);

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset(['current_password', 'password', 'password_confirmation']);

        $this->dispatch('password-updated');
    }
}; ?>

<flux:card class="space-y-6">
    <div>
        <flux:heading size="lg">{{ __('Update Password') }}</flux:heading>
        <flux:subheading>{{ __('Ensure your account is using a long, random password to stay secure.') }}</flux:subheading>
    </div>

    <form wire:submit="updatePassword" class="space-y-6">
        <flux:field>
            <flux:label>{{ __('Current Password') }}</flux:label>
            <flux:input wire:model="current_password" type="password" autocomplete="current-password" viewable />
            <flux:error name="current_password" />
        </flux:field>

        <flux:field>
            <flux:label>{{ __('New Password') }}</flux:label>
            <flux:input wire:model="password" type="password" autocomplete="new-password" viewable />
            <flux:error name="password" />
        </flux:field>

        <flux:field>
            <flux:label>{{ __('Confirm Password') }}</flux:label>
            <flux:input wire:model="password_confirmation" type="password" autocomplete="new-password" viewable />
            <flux:error name="password_confirmation" />
        </flux:field>

        <div class="flex items-center gap-4">
            <flux:button type="submit" variant="primary">{{ __('Save') }}</flux:button>

            <div x-data="{ shown: false }" x-on:password-updated.window="shown = true; setTimeout(() => shown = false, 2000)">
                <flux:text x-show="shown" x-transition class="text-sm text-green-600 dark:text-green-400">
                    {{ __('Saved.') }}
                </flux:text>
            </div>
        </div>
    </form>
</flux:card>
```

**Step 2: Commit**

```bash
git add resources/views/livewire/settings/password.blade.php
git commit -m "feat: add password SFC component"
```

---

## Task 8: Create Two-Factor SFC Component

**Files:**
- Create: `resources/views/livewire/settings/two-factor.blade.php`

**Step 1: Create two-factor SFC**

Create `resources/views/livewire/settings/two-factor.blade.php`:

```php
<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Actions\ConfirmTwoFactorAuthentication;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;
use Laravel\Fortify\Actions\GenerateNewRecoveryCodes;
use Livewire\Volt\Component;

new class extends Component {
    public bool $showingQrCode = false;
    public bool $showingConfirmation = false;
    public bool $showingRecoveryCodes = false;
    public string $code = '';
    public string $password = '';
    public bool $confirmingDisable = false;

    public function getEnabledProperty(): bool
    {
        return ! empty(Auth::user()->two_factor_secret);
    }

    public function getConfirmedProperty(): bool
    {
        return ! empty(Auth::user()->two_factor_confirmed_at);
    }

    public function enableTwoFactor(): void
    {
        $this->validatePassword();

        app(EnableTwoFactorAuthentication::class)(Auth::user());

        $this->showingQrCode = true;
        $this->showingConfirmation = true;
        $this->password = '';
    }

    public function confirmTwoFactor(): void
    {
        $this->validate(['code' => ['required', 'string']]);

        app(ConfirmTwoFactorAuthentication::class)(Auth::user(), $this->code);

        $this->showingQrCode = false;
        $this->showingConfirmation = false;
        $this->showingRecoveryCodes = true;
        $this->code = '';
    }

    public function showRecoveryCodes(): void
    {
        $this->validatePassword();
        $this->showingRecoveryCodes = true;
        $this->password = '';
    }

    public function regenerateRecoveryCodes(): void
    {
        $this->validatePassword();
        app(GenerateNewRecoveryCodes::class)(Auth::user());
        $this->showingRecoveryCodes = true;
        $this->password = '';
    }

    public function disableTwoFactor(): void
    {
        $this->validatePassword();
        app(DisableTwoFactorAuthentication::class)(Auth::user());

        $this->showingQrCode = false;
        $this->showingConfirmation = false;
        $this->showingRecoveryCodes = false;
        $this->confirmingDisable = false;
        $this->password = '';
    }

    protected function validatePassword(): void
    {
        $this->validate(['password' => ['required', 'string']]);

        if (! Hash::check($this->password, Auth::user()->password)) {
            throw ValidationException::withMessages([
                'password' => [__('This password does not match our records.')],
            ]);
        }
    }
}; ?>

<flux:card class="space-y-6">
    <div>
        <flux:heading size="lg">{{ __('Two Factor Authentication') }}</flux:heading>
        <flux:subheading>{{ __('Add additional security to your account using two factor authentication.') }}</flux:subheading>
    </div>

    <div class="space-y-4">
        <div class="flex items-center gap-3">
            @if ($this->enabled && $this->confirmed)
                <flux:badge color="green" size="sm">{{ __('Enabled') }}</flux:badge>
            @elseif ($this->enabled)
                <flux:badge color="amber" size="sm">{{ __('Pending') }}</flux:badge>
            @else
                <flux:badge color="zinc" size="sm">{{ __('Disabled') }}</flux:badge>
            @endif
        </div>

        <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">
            {{ __("When two factor authentication is enabled, you will be prompted for a secure, random token during authentication.") }}
        </flux:text>

        @if ($this->enabled)
            @if ($showingQrCode)
                <div class="space-y-4">
                    <flux:text class="text-sm font-medium">
                        @if ($showingConfirmation)
                            {{ __("Scan the QR code with your authenticator app and enter the code to confirm.") }}
                        @else
                            {{ __("Two factor authentication is enabled. Scan the QR code with your authenticator app.") }}
                        @endif
                    </flux:text>

                    <div class="p-4 inline-block bg-white rounded-lg">
                        {!! Auth::user()->twoFactorQrCodeSvg() !!}
                    </div>

                    <div class="p-3 bg-zinc-100 dark:bg-zinc-800 rounded-lg">
                        <flux:text class="text-sm font-medium">
                            {{ __('Setup Key') }}: <code class="font-mono">{{ decrypt(Auth::user()->two_factor_secret) }}</code>
                        </flux:text>
                    </div>

                    @if ($showingConfirmation)
                        <flux:field class="max-w-xs">
                            <flux:label>{{ __('Code') }}</flux:label>
                            <flux:input wire:model="code" type="text" inputmode="numeric" autofocus autocomplete="one-time-code" wire:keydown.enter="confirmTwoFactor" placeholder="000000" />
                            <flux:error name="code" />
                        </flux:field>
                    @endif
                </div>
            @endif

            @if ($showingRecoveryCodes)
                <div class="space-y-4">
                    <flux:text class="text-sm font-medium">
                        {{ __('Store these recovery codes in a secure password manager.') }}
                    </flux:text>

                    <div class="p-4 bg-zinc-100 dark:bg-zinc-800 rounded-lg font-mono text-sm grid gap-1">
                        @foreach (json_decode(decrypt(Auth::user()->two_factor_recovery_codes), true) as $code)
                            <div>{{ $code }}</div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endif

        {{-- Action Buttons --}}
        <div class="flex flex-wrap gap-3 pt-2">
            @if (! $this->enabled)
                {{-- Enable Button with Password --}}
                <div x-data="{ open: false }">
                    <flux:button variant="primary" x-on:click="open = true">{{ __('Enable') }}</flux:button>

                    <flux:modal x-model="open" class="max-w-md">
                        <flux:heading size="lg">{{ __('Confirm Password') }}</flux:heading>
                        <flux:text class="mt-2">{{ __('Please confirm your password to enable two-factor authentication.') }}</flux:text>

                        <flux:field class="mt-4">
                            <flux:label>{{ __('Password') }}</flux:label>
                            <flux:input wire:model="password" type="password" viewable />
                            <flux:error name="password" />
                        </flux:field>

                        <div class="mt-6 flex justify-end gap-3">
                            <flux:button variant="ghost" x-on:click="open = false">{{ __('Cancel') }}</flux:button>
                            <flux:button variant="primary" wire:click="enableTwoFactor" x-on:click="open = false">{{ __('Enable') }}</flux:button>
                        </div>
                    </flux:modal>
                </div>
            @else
                @if ($showingConfirmation)
                    <flux:button variant="primary" wire:click="confirmTwoFactor">{{ __('Confirm') }}</flux:button>
                    <flux:button variant="ghost" wire:click="$set('showingConfirmation', false)">{{ __('Cancel') }}</flux:button>
                @elseif ($showingRecoveryCodes)
                    {{-- Regenerate with password confirmation --}}
                    <div x-data="{ open: false }">
                        <flux:button variant="ghost" x-on:click="open = true">{{ __('Regenerate Recovery Codes') }}</flux:button>

                        <flux:modal x-model="open" class="max-w-md">
                            <flux:heading size="lg">{{ __('Confirm Password') }}</flux:heading>
                            <flux:field class="mt-4">
                                <flux:label>{{ __('Password') }}</flux:label>
                                <flux:input wire:model="password" type="password" viewable />
                                <flux:error name="password" />
                            </flux:field>
                            <div class="mt-6 flex justify-end gap-3">
                                <flux:button variant="ghost" x-on:click="open = false">{{ __('Cancel') }}</flux:button>
                                <flux:button variant="primary" wire:click="regenerateRecoveryCodes" x-on:click="open = false">{{ __('Regenerate') }}</flux:button>
                            </div>
                        </flux:modal>
                    </div>

                    <flux:button variant="ghost" wire:click="$set('showingRecoveryCodes', false)">{{ __('Close') }}</flux:button>
                @else
                    {{-- Show Recovery Codes with password --}}
                    <div x-data="{ open: false }">
                        <flux:button variant="ghost" x-on:click="open = true">{{ __('Show Recovery Codes') }}</flux:button>

                        <flux:modal x-model="open" class="max-w-md">
                            <flux:heading size="lg">{{ __('Confirm Password') }}</flux:heading>
                            <flux:field class="mt-4">
                                <flux:label>{{ __('Password') }}</flux:label>
                                <flux:input wire:model="password" type="password" viewable />
                                <flux:error name="password" />
                            </flux:field>
                            <div class="mt-6 flex justify-end gap-3">
                                <flux:button variant="ghost" x-on:click="open = false">{{ __('Cancel') }}</flux:button>
                                <flux:button variant="primary" wire:click="showRecoveryCodes" x-on:click="open = false">{{ __('Show') }}</flux:button>
                            </div>
                        </flux:modal>
                    </div>
                @endif

                {{-- Disable with password confirmation --}}
                <div x-data="{ open: false }">
                    <flux:button variant="danger" x-on:click="open = true">{{ __('Disable') }}</flux:button>

                    <flux:modal x-model="open" class="max-w-md">
                        <flux:heading size="lg">{{ __('Disable Two-Factor Authentication') }}</flux:heading>
                        <flux:text class="mt-2">{{ __('Please confirm your password to disable two-factor authentication.') }}</flux:text>

                        <flux:field class="mt-4">
                            <flux:label>{{ __('Password') }}</flux:label>
                            <flux:input wire:model="password" type="password" viewable />
                            <flux:error name="password" />
                        </flux:field>

                        <div class="mt-6 flex justify-end gap-3">
                            <flux:button variant="ghost" x-on:click="open = false">{{ __('Cancel') }}</flux:button>
                            <flux:button variant="danger" wire:click="disableTwoFactor" x-on:click="open = false">{{ __('Disable') }}</flux:button>
                        </div>
                    </flux:modal>
                </div>
            @endif
        </div>
    </div>
</flux:card>
```

**Step 2: Commit**

```bash
git add resources/views/livewire/settings/two-factor.blade.php
git commit -m "feat: add two-factor SFC component"
```

---

## Task 9: Create Sessions SFC Component

**Files:**
- Create: `resources/views/livewire/settings/sessions.blade.php`

**Step 1: Create sessions SFC**

Create `resources/views/livewire/settings/sessions.blade.php`:

```php
<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Jenssegers\Agent\Agent;
use Livewire\Volt\Component;

new class extends Component {
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
        return tap(new Agent(), fn ($agent) => $agent->setUserAgent($session->user_agent));
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
```

**Step 2: Commit**

```bash
git add resources/views/livewire/settings/sessions.blade.php
git commit -m "feat: add sessions SFC component"
```

---

## Task 10: Create Delete Account SFC Component

**Files:**
- Create: `resources/views/livewire/settings/delete-account.blade.php`

**Step 1: Create delete-account SFC**

Create `resources/views/livewire/settings/delete-account.blade.php`:

```php
<?php

use App\Actions\DeleteUser;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component {
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
```

**Step 2: Commit**

```bash
git add resources/views/livewire/settings/delete-account.blade.php
git commit -m "feat: add delete-account SFC component"
```

---

## Task 11: Update Navigation to Use New Settings Route

**Files:**
- Modify: `resources/views/components/layouts/app.blade.php`

**Step 1: Update profile link in header**

Find the profile menu item and update route from `profile.show` to `settings`:

```blade
<flux:menu.item href="{{ route('settings') }}" icon="user">Profile</flux:menu.item>
```

**Step 2: Commit**

```bash
git add resources/views/components/layouts/app.blade.php
git commit -m "refactor: update navigation to use new settings route"
```

---

## Task 12: Remove Old Profile Views

**Files:**
- Delete: `resources/views/profile/` directory
- Delete: Old Jetstream component views that are no longer needed

**Step 1: Remove profile directory**

Run:
```bash
rm -rf resources/views/profile
```

**Step 2: Commit**

```bash
git rm -rf resources/views/profile
git commit -m "chore: remove old Jetstream profile views"
```

---

## Task 13: Remove Jetstream Package

**Files:**
- Modify: `composer.json`
- Delete: `config/jetstream.php`

**Step 1: Remove Jetstream package**

Run:
```bash
composer remove laravel/jetstream --no-interaction
```

**Step 2: Remove Jetstream config**

Run:
```bash
rm config/jetstream.php
```

**Step 3: Verify application boots**

Run: `php artisan route:list | head -10`
Expected: Routes list without errors

**Step 4: Commit**

```bash
git add composer.json composer.lock
git rm config/jetstream.php
git commit -m "chore: remove laravel/jetstream package"
```

---

## Task 14: Install jenssegers/agent Package

**Files:**
- Modify: `composer.json`

The sessions component needs the Agent package for browser/device detection.

**Step 1: Install agent package**

Run:
```bash
composer require jenssegers/agent --no-interaction
```

**Step 2: Commit**

```bash
git add composer.json composer.lock
git commit -m "chore: add jenssegers/agent for browser detection"
```

---

## Task 15: Test and Verify

**Step 1: Clear caches**

Run:
```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

**Step 2: Verify routes**

Run: `php artisan route:list --path=settings`
Expected: Shows settings route

**Step 3: Run Pint**

Run: `vendor/bin/pint --dirty`

**Step 4: Run tests**

Run: `php artisan test`
Expected: All tests pass (may need to update tests that reference Jetstream)

**Step 5: Manual testing checklist**

- [ ] Visit /settings - page loads
- [ ] Update profile name/email - works
- [ ] Update password - works
- [ ] Enable/disable 2FA - works
- [ ] View recovery codes - works
- [ ] Log out other sessions - works
- [ ] Delete account - works

**Step 6: Final commit**

```bash
git add .
git commit -m "chore: cleanup and verify Jetstream removal"
```

---

## Summary

After completing all tasks:

1. **Removed:** `laravel/jetstream` package
2. **Kept:** `laravel/fortify` (direct dependency now)
3. **Kept:** `laravel/sanctum` for API tokens
4. **Created:** 5 Livewire 4 SFC components in `/resources/views/livewire/settings/`
5. **Created:** 1 Folio page at `/resources/views/pages/settings/index.blade.php`
6. **Removed:** Old Jetstream profile views and provider

The application now uses clean Livewire 4 SFC components with direct Fortify integration, no Jetstream abstraction layer.
