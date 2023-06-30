<div @class([
    'grid gap-10 sm:grid-cols-2' => $hasTestCredentials(),
])>
    {{-- production credentials --}}
    <x-easypost::carrier-field-section
        :title="__('easypost::labels.carrier_account_form.production_credentials')"
    >
        @foreach ($productionCredentials() as $credentialId => $credential)
            <x-easypost::carrier-account-field
                :credential="$credential"
                :credential-id="$credentialId"
                name-prefix="credentials"
                :id-prefix="$idPrefix"
                :is-create="$isCreate"
            />
        @endforeach
    </x-easypost::carrier-field-section>

    {{-- test credentials --}}
    @if ($hasTestCredentials())
        <x-easypost::carrier-field-section
            :title="__('easypost::labels.carrier_account_form.test_credentials')"
        >
            @foreach ($testCredentials() as $credentialId => $credential)
                <x-easypost::carrier-account-field
                    :credential="$credential"
                    :credential-id="$credentialId"
                    name-prefix="test_credentials"
                    :id-prefix="$idPrefix"
                    is-test-env
                    :is-create="$isCreate"
                />
            @endforeach
        </x-easypost::carrier-field-section>
    @endif
</div>
