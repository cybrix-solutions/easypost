@include('easypost::components.partials.carrier-fields.accept-license', [
    'licenseUrl' => route('easypost::legal.ups_license'),
])

<div class="grid gap-10 sm:grid-cols-3">
    {{-- account information --}}
    <x-easypost::carrier-field-section
        :title="__('easypost::labels.carrier_account_form.ups.account_info')"
        :help="__('easypost::labels.carrier_account_form.ups.account_info_help')"
    >
        @foreach ($customCredentials()['account'] as $credential)
            <x-easypost::carrier-account-field
                :credential="$credential"
                :credential-id="$credential->name()"
                name-prefix="registration_data"
                id-prefix="new_carrier_account"
            />
        @endforeach
    </x-easypost::carrier-field-section>

    {{-- company information --}}
    <x-easypost::carrier-field-section :title="__('easypost::labels.carrier_account_form.ups.company')">
        @foreach ($customCredentials()['company'] as $credential)
            <x-easypost::carrier-account-field
                :credential="$credential"
                :credential-id="$credential->name()"
                name-prefix="registration_data"
                id-prefix="new_carrier_account"
            />
        @endforeach
    </x-easypost::carrier-field-section>

    {{-- address information --}}
    <x-easypost::carrier-field-section :title="__('easypost::labels.carrier_account_form.ups.address')">
        @foreach ($customCredentials()['address'] as $credential)
            <x-easypost::carrier-account-field
                :credential="$credential"
                :credential-id="$credential->name()"
                name-prefix="registration_data"
                id-prefix="new_carrier_account"
            />
        @endforeach
    </x-easypost::carrier-field-section>
</div>
