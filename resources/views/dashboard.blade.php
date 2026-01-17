<x-layouts.app :show-sidebar="false" title="Dashboard">
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <flux:heading size="xl">{{ __('Welcome back, :name', ['name' => Auth::user()->name]) }}</flux:heading>
            <flux:subheading>{{ __('Manage your account and access tools from your dashboard.') }}</flux:subheading>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Account Settings --}}
            <flux:card>
                <div class="p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="p-2 bg-zinc-100 dark:bg-zinc-800 rounded-lg">
                            <flux:icon.cog-6-tooth class="size-6 text-zinc-600 dark:text-zinc-400" />
                        </div>
                        <flux:heading size="lg">{{ __('Account Settings') }}</flux:heading>
                    </div>
                    <flux:text class="mb-4">{{ __('Update your profile, change your password, and manage security settings.') }}</flux:text>
                    <flux:button href="{{ route('settings') }}" variant="ghost" icon-trailing="arrow-right">
                        {{ __('Go to Settings') }}
                    </flux:button>
                </div>
            </flux:card>

            {{-- Video Manager - Only shown if user can manage videos --}}
            @can('manage-videos')
                <flux:card>
                    <div class="p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                                <flux:icon.film class="size-6 text-blue-600 dark:text-blue-400" />
                            </div>
                            <flux:heading size="lg">{{ __('Video Manager') }}</flux:heading>
                        </div>
                        <flux:text class="mb-4">{{ __('Upload, manage, and publish video content for your documentation.') }}</flux:text>
                        <flux:button href="{{ route('admin.video') }}" variant="ghost" icon-trailing="arrow-right">
                            {{ __('Manage Videos') }}
                        </flux:button>
                    </div>
                </flux:card>
            @endcan

            {{-- Documentation --}}
            <flux:card>
                <div class="p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                            <flux:icon.book-open class="size-6 text-green-600 dark:text-green-400" />
                        </div>
                        <flux:heading size="lg">{{ __('Documentation') }}</flux:heading>
                    </div>
                    <flux:text class="mb-4">{{ __('Browse the Flow Forms documentation and guides.') }}</flux:text>
                    <flux:button href="/" variant="ghost" icon-trailing="arrow-right">
                        {{ __('View Docs') }}
                    </flux:button>
                </div>
            </flux:card>
        </div>
    </div>
</x-layouts.app>
