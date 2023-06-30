<button
    type="button"
    wire:key="carrierType{{ $carrier->value }}"
    wire:click="selectCarrier('{{ $carrier->value }}')"
    wire:loading.attr="disabled"
    wire:target="selectCarrier"
    {{ $attributes->except(['type'])->class('border rounded-md border-gray-200 dark:border-gray-500 py-3 hover:bg-gray-200 dark:hover:bg-gray-500') }}
>
    <div
        wire:loading.class.delay="hidden"
        wire:target="selectCarrier('{{ $carrier->value }}')"
    >
        <div
            style="background-image: url({{ $carrier->image() }});"
            class="w-[6.5rem] h-[6.5rem] bg-no-repeat bg-contain bg-[50%] mx-auto mb-2"
        >
        </div>

        <span class="text-sm text-gray-600 dark:text-gray-200">
            {{ $carrier->label() }}
        </span>
    </div>

    <div class="hidden justify-center items-center"
         wire:loading.class.remove.delay="hidden"
         wire:loading.class.delay="flex"
         wire:target="selectCarrier('{{ $carrier->value }}')"
    >
        <x-easypost::loaders.spinner class="w-8 h-8" />
    </div>
</button>
