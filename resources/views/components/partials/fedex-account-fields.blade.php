@include('easypost::components.partials.carrier-fields.accept-license', [
    'licenseUrl' => 'https://assets.easypost.com/assets/pdfs/fedex_user_reg_eula.2ea68926ba2d33b1539310012ba091fd.pdf',
])

<div class="grid gap-10 sm:grid-cols-3" id="new-fedex-account-fields">
    {{-- credential information --}}
    <x-easypost::carrier-field-section :title="__('easypost::labels.carrier_account_form.fedex.account_info')">
        @foreach ($customCredentials()['credential_information'] as $credential)
            <x-easypost::carrier-account-field
                :credential="$credential"
                :credential-id="$credential->name()"
                name-prefix="registration_data"
                id-prefix="new_carrier_account"
            />
        @endforeach
    </x-easypost::carrier-field-section>

    {{-- company information --}}
    <x-easypost::carrier-field-section :title="__('easypost::labels.carrier_account_form.fedex.company')">
        @foreach ($customCredentials()['company_information'] as $credential)
            <x-easypost::carrier-account-field
                :credential="$credential"
                :credential-id="$credential->name()"
                name-prefix="registration_data"
                id-prefix="new_carrier_account"
            />
        @endforeach
    </x-easypost::carrier-field-section>

    {{-- address information --}}
    <x-easypost::carrier-field-section
        :title="__('easypost::labels.carrier_account_form.fedex.address')"
        :help="__('easypost::labels.carrier_account_form.fedex.address_help')"
    >
        @foreach ($customCredentials()['address_information'] as $credential)
            <x-easypost::carrier-account-field
                :credential="$credential"
                :credential-id="$credential->name()"
                name-prefix="registration_data"
                id-prefix="new_carrier_account"
            />
        @endforeach
    </x-easypost::carrier-field-section>
</div>
