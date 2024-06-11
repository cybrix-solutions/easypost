<div class="mt-2 fi-easypost-carrier-signup">
    <p>{{ __('easypost::labels.need_carrier_account_title') }}</p>

    <x-easypost::filament.carriers.signup
        :service="$this->selectedCarrierService"
    />
</div>
