<div class="relative flex justify-between gap-x-6 py-4 px-4 border rounded-md">
    <div>
        <div class="min-w-0 flex-auto">
            <div class="flex items-center gap-x-2">
                <p class="text-sm font-semibold leading-6 text-gray-900 dark:text-white">
                    {{ $webhookType() }}
                </p>

                @includeWhen(! $webhook->isActive(), 'easypost::components.partials.webhooks.inactive-badge')
            </div>

            <p class="text-sm mt-1.5 leading-4 text-gray-900 dark:text-white">
                {{ $webhook->url }}
            </p>

            <p class="mt-2 text-xs leading-5 text-gray-500 dark:text-gray-200">
                {{ __('easypost::webhooks.labels.webhook_id', ['id' => $webhook->id]) }}
            </p>
        </div>
    </div>

    <div>
        @include('easypost::components.partials.webhooks.webhook-actions')
    </div>
</div>
