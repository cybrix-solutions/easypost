<div>
    <x-filament::section
        :heading="__('easypost::livewire/webhooks.heading')"
        :description="__('easypost::livewire/webhooks.description')"
    >
        @env('local')
            <x-slot:header-end>
                {{ $this->addWebhookAction }}
            </x-slot:header-end>
        @endenv

        @includeWhen(! $this->hasProductionWebhook, 'easypost::livewire.partials.missing-production-webhook-warning')

        <x-filament-tables::container
            @class([
                'mt-4' => ! $this->hasProductionWebhook,
            ])
        >
            <div class="fi-ta-content divide-y divide-gray-200 overflow-x-auto dark:divide-white/10 dark:border-t-white/10 !border-t-0">
                <x-filament-tables::table>
                    <x-slot:header>
                        <x-filament-tables::header-cell>
                            {{ __('easypost::livewire/webhooks.table.url.label') }}
                        </x-filament-tables::header-cell>

                        <x-filament-tables::header-cell>
                            {{ __('easypost::livewire/webhooks.table.mode.label') }}
                        </x-filament-tables::header-cell>

                        <th class="w-1"></th>
                    </x-slot:header>

                    @forelse ($this->webhooks as $webhook)
                        <x-filament-tables::row
                            :wire:key="'webhooks.' . $webhook->id"
                        >
                            <x-filament-tables::cell>
                                <div class="fi-ta-col-wrp">
                                    <div class="fi-ta-text px-3 py-4">
                                        <div
                                            x-data
                                            x-tooltip="{
                                                content: @js($webhook->url),
                                                theme: $store.theme,
                                            }"
                                        >
                                            {{ Str::limit($webhook->url) }}
                                        </div>

                                        <div class="text-xs mt-1 5 text-gray-500 dark:text-gray-400">
                                            {{ __('easypost::livewire/webhooks.table.id.label', ['id' => $webhook->id]) }}
                                        </div>
                                    </div>
                                </div>
                            </x-filament-tables::cell>

                            <x-filament-tables::cell>
                                <div class="fi-ta-col-wrp">
                                    <div class="fi-ta-text px-3 py-4 inline-flex">
                                        <x-filament::badge
                                            :color="$webhook->mode === 'production' ? 'success' : 'primary'"
                                        >
                                            {{ $webhook->mode === 'production' ? __('easypost::livewire/webhooks.modes.production') : __('easypost::livewire/webhooks.modes.test') }}
                                        </x-filament::badge>
                                    </div>
                                </div>
                            </x-filament-tables::cell>

                            <x-filament-tables::actions.cell>
                                {{ ($this->deleteWebhookAction)(['webhook' => $webhook->id, 'url' => $webhook->url]) }}
                            </x-filament-tables::actions.cell>
                        </x-filament-tables::row>
                    @empty
                        <tr>
                            <td colspan="3">
                                <x-filament-tables::empty-state
                                    class="whitespace-normal"
                                    :icon="\Filament\Support\Facades\FilamentIcon::resolve('easypost::empty-webhooks') ?? 'heroicon-o-circle-stack'"
                                    :heading="__('easypost::livewire/webhooks.table.empty.heading')"
                                    :description="__('easypost::livewire/webhooks.table.empty.description')"
                                />
                            </td>
                        </tr>
                    @endforelse
                </x-filament-tables::table>
            </div>
        </x-filament-tables::container>
    </x-filament::section>

    <x-filament-actions::modals />
</div>
