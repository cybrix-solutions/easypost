<fieldset
    {{ $attributes->except('title') }}
    x-data="{ showHelp: false }"
    wire:ignore.self
>
    <legend
        class="text-gray-900 dark:text-white text-lg border-b border-gray-200 dark:border-gray-500 w-full pb-1"
        x-bind:class="{ 'mb-2': showHelp, 'mb-5' : ! showHelp }"
    >
        <span class="flex items-center justify-between gap-x-2">
            <span>{{ $title }}</span>

            <div>
                <button
                    type="button"
                    class="text-xs text-blue-500 hover:underline"
                    x-on:click="showHelp = ! showHelp"
                >
                    {{ __('easypost::labels.carrier_account_form.section_help_button') }}
                </button>
            </div>
        </span>
    </legend>

    <p class="text-sm italic mb-4" x-show="showHelp">
        {{ $help }}
    </p>

    {{ $slot }}
</fieldset>
