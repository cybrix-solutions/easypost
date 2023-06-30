<div class="mb-4 last:mb-0">
    @unless ($credential->isCheckbox())
        <label
            for="{{ $inputId }}"
            class="block text-base font-medium leading-5 text-gray-700"
        >
            <span>{{ $credential->label() }}</span>
            @if (! $isCreate && $credential->isPassword())
                <div class="text-xs font-normal italic">
                    {{ __('easypost::labels.carrier_account_form.masked_field_info') }}
                </div>
            @endif
        </label>
    @endunless

    <div @class(['mt-1' => ! $credential->isCheckbox()])>
        @include($inputPartial)

        @error ($inputName)
            <div class="mt-1 text-sm text-red-500">
                {{ $message }}
            </div>
        @enderror
    </div>
</div>
