@props([
    'carrier',
    'method' => 'selectCarrierType',
])

@php
    /** @var \CybrixSolutions\EasyPost\Enums\CarrierEnum $carrier */

    $wireClickTarget = "{$method}('{$carrier->value}')";
    $loadingIndicatorTarget = html_entity_decode($wireClickTarget, ENT_QUOTES);
@endphp

<button
    type="button"
    wire:key="carrierType.{{ $carrier->value }}"
    wire:loading.attr="disabled"
    wire:click="{{ $wireClickTarget }}"
    wire:target="{{ $loadingIndicatorTarget }}"
    {{ $attributes->except('type')->class('border rounded-md border-gray-300 dark:border-gray-500 py-3 hover:bg-gray-300 dark:hover:bg-gray-500') }}
>
    <div
        wire:loading.class.delay="hidden"
        wire:target="{{ $loadingIndicatorTarget }}"
    >
        <div
            class="w-[6.5rem] h-[6.5rem] bg-no-repeat bg-contain bg-[50%] mx-auto mb-2"
            style="background-image: url(@js($carrier->image())"
        >
        </div>

        <span class="text-sm">
            {{ $carrier->label() }}
        </span>
    </div>

    <x-filament::loading-indicator
        wire:loading.delay=""
        :wire:target="$loadingIndicatorTarget"
        class="h-8 w-8"
    />
</button>
