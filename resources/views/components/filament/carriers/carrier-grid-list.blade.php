@props([
    'carrierTypes' => [],
    'selectCarrierMethod' => 'selectCarrierType',
])

<div class="py-6">
    <div class="grid sm:grid-cols-4 gap-6 max-h-[250px] sm:max-h-[600px] md:max-h-[800px] overflow-auto px-2">
        @forelse ($carrierTypes as $type)
            <x-easypost::filament.carriers.carrier-button
                :carrier="$type"
                :method="$selectCarrierMethod"
            />
        @empty
            <div class="sm:col-span-4">
                <div class="text-center text-lg my-5">
                    {{ __('easypost::labels.carrier_account_form.no_search_results') }}
                </div>
            </div>
        @endforelse
    </div>
</div>
