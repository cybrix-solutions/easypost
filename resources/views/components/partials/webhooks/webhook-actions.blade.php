<div class="h-full flex justify-center items-center">
    <div>
        <a class="text-red-500 hover:text-red-400 hover:underline text-xs"
           role="button"
           wire:click.prevent="$emit('webhook.confirm_delete', '{{ $webhook->id }}', '{{ $webhook->mode }}')"
        >
            {{ __('easypost::webhooks.labels.delete_button') }}
        </a>
    </div>
</div>
