<flux:card class="space-y-6">
    <div>
        <flux:heading size="lg">{{ __('Profile Information') }}</flux:heading>
        <flux:subheading>{{ __("Update your account's profile information and email address.") }}</flux:subheading>
    </div>

    <form wire:submit="updateProfileInformation" class="space-y-6">
        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
            <div x-data="{ photoName: null, photoPreview: null }">
                <input
                    type="file"
                    id="photo"
                    class="hidden"
                    wire:model.live="photo"
                    x-ref="photo"
                    x-on:change="
                        photoName = $refs.photo.files[0].name;
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            photoPreview = e.target.result;
                        };
                        reader.readAsDataURL($refs.photo.files[0]);
                    "
                />

                <flux:field>
                    <flux:label>{{ __('Photo') }}</flux:label>

                    <div class="mt-2" x-show="! photoPreview">
                        <img src="{{ $this->user->profile_photo_url }}" alt="{{ $this->user->name }}" class="rounded-full size-20 object-cover">
                    </div>

                    <div class="mt-2" x-show="photoPreview" style="display: none;">
                        <span class="block rounded-full size-20 bg-cover bg-no-repeat bg-center" x-bind:style="'background-image: url(\'' + photoPreview + '\');'"></span>
                    </div>

                    <div class="mt-2 flex gap-2">
                        <flux:button variant="ghost" type="button" x-on:click.prevent="$refs.photo.click()">
                            {{ __('Select A New Photo') }}
                        </flux:button>

                        @if ($this->user->profile_photo_path)
                            <flux:button variant="ghost" type="button" wire:click="deleteProfilePhoto">
                                {{ __('Remove Photo') }}
                            </flux:button>
                        @endif
                    </div>

                    @error('photo')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </flux:field>
            </div>
        @endif

        <flux:field>
            <flux:label>{{ __('Name') }}</flux:label>
            <flux:input
                wire:model="state.name"
                type="text"
                required
                autocomplete="name"
            />
            @error('name')
                <flux:error>{{ $message }}</flux:error>
            @enderror
        </flux:field>

        <flux:field>
            <flux:label>{{ __('Email') }}</flux:label>
            <flux:input
                wire:model="state.email"
                type="email"
                required
                autocomplete="email"
            />
            @error('email')
                <flux:error>{{ $message }}</flux:error>
            @enderror

            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::emailVerification()) && ! $this->user->hasVerifiedEmail())
                <div class="mt-2">
                    <flux:text class="text-sm">
                        {{ __('Your email address is unverified.') }}
                        <flux:link variant="subtle" wire:click.prevent="sendEmailVerification" class="cursor-pointer">
                            {{ __('Click here to re-send the verification email.') }}
                        </flux:link>
                    </flux:text>

                    @if ($this->verificationLinkSent)
                        <flux:text class="mt-2 text-sm text-green-600 dark:text-green-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </flux:text>
                    @endif
                </div>
            @endif
        </flux:field>

        <div class="flex items-center gap-4">
            <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="photo">
                {{ __('Save') }}
            </flux:button>

            <x-action-message on="saved">
                <flux:text class="text-sm text-green-600 dark:text-green-400">{{ __('Saved.') }}</flux:text>
            </x-action-message>
        </div>
    </form>
</flux:card>
