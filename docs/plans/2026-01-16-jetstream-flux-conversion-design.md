# Jetstream to Flux UI Conversion Design

## Overview

Convert all Jetstream authentication and profile management views from custom Blade components to Flux UI components, inspired by the [Laravel Livewire Starter Kit](https://github.com/laravel/livewire-starter-kit).

## Scope

**Full conversion including:**
- Auth pages (login, register, password reset, etc.)
- Profile management (profile info, password, 2FA, sessions, delete account)
- All Jetstream-related views

## Design Decisions

### Layout Approach
- **Auth pages**: Centered card with logo above (classic auth page style)
- **Profile page**: Stacked sections (all settings visible vertically on one page with separators)

### Flux Components to Use

| Purpose | Component |
|---------|-----------|
| Form inputs | `flux:input` with `viewable` for passwords |
| 2FA codes | `flux:input:otp length="6"` |
| Buttons | `flux:button variant="primary/danger/ghost"` |
| Cards | `flux:card` |
| Typography | `flux:heading`, `flux:subheading`, `flux:text` |
| Status | `flux:badge color="green/red"` |
| Messages | `flux:callout variant="success/danger"` |
| Modals | `flux:modal`, `flux:modal.trigger`, `flux:modal.close` |
| Dividers | `flux:separator` |

### Auth Page Pattern

```blade
<x-layouts.auth.card>
    <x-auth-header :title="__('Log in')" :description="__('Enter your credentials')" />

    <form wire:submit="login" class="flex flex-col gap-6">
        <flux:input wire:model="email" label="Email" type="email" required autofocus />
        <flux:input wire:model="password" label="Password" type="password" viewable />

        <flux:button type="submit" variant="primary" class="w-full">Log in</flux:button>
    </form>
</x-layouts.auth.card>
```

### Profile Section Pattern (Stacked)

```blade
<flux:card class="space-y-6">
    <div>
        <flux:heading size="lg">Section Title</flux:heading>
        <flux:subheading>Description text.</flux:subheading>
    </div>

    <form wire:submit="updateMethod" class="space-y-6">
        {{-- Form fields --}}

        <div class="flex items-center gap-4">
            <flux:button type="submit" variant="primary">Save</flux:button>
            <x-action-message on="event-name">Saved.</x-action-message>
        </div>
    </form>
</flux:card>
```

### Modal Pattern

```blade
<flux:modal name="confirm-action" class="max-w-lg">
    <flux:heading size="lg">Confirm Action</flux:heading>
    <flux:text class="mt-2">Are you sure?</flux:text>

    <flux:input type="password" wire:model="password" label="Password" viewable class="mt-4" />

    <div class="mt-6 flex gap-3">
        <flux:modal.close>
            <flux:button variant="ghost">Cancel</flux:button>
        </flux:modal.close>
        <flux:button variant="danger" wire:click="confirmAction">Confirm</flux:button>
    </div>
</flux:modal>

<flux:modal.trigger name="confirm-action">
    <flux:button variant="danger">Delete</flux:button>
</flux:modal.trigger>
```

## Files to Modify

### Auth Pages (already published)
- `resources/views/auth/login.blade.php`
- `resources/views/auth/register.blade.php`
- `resources/views/auth/forgot-password.blade.php`
- `resources/views/auth/reset-password.blade.php`
- `resources/views/auth/confirm-password.blade.php`
- `resources/views/auth/verify-email.blade.php`
- `resources/views/auth/two-factor-challenge.blade.php`

### Profile Views (need to publish)
- `resources/views/profile/show.blade.php`

### Profile Livewire Components (need to publish)
- `resources/views/livewire/profile/update-profile-information-form.blade.php`
- `resources/views/livewire/profile/update-password-form.blade.php`
- `resources/views/livewire/profile/two-factor-authentication-form.blade.php`
- `resources/views/livewire/profile/logout-other-browser-sessions-form.blade.php`
- `resources/views/livewire/profile/delete-user-form.blade.php`

### New Layouts/Components
- `resources/views/layouts/auth.blade.php` - Base auth layout
- `resources/views/layouts/auth/card.blade.php` - Centered card variant
- `resources/views/components/auth-header.blade.php` - Auth page header

## Implementation Steps

1. Publish Jetstream views (`php artisan vendor:publish --tag=jetstream-views`)
2. Create auth layout components
3. Convert auth pages (login, register, etc.)
4. Convert profile page wrapper
5. Convert profile Livewire components
6. Test all auth and profile flows
7. Run Pint for code formatting

## Testing Checklist

- [ ] Login flow works
- [ ] Registration flow works
- [ ] Password reset flow works
- [ ] Email verification flow works
- [ ] 2FA challenge works
- [ ] Profile update works
- [ ] Password change works
- [ ] 2FA enable/disable works
- [ ] Session logout works
- [ ] Account deletion works
- [ ] All forms show validation errors correctly
- [ ] Dark mode works on all pages
