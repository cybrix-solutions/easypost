<div class="mt-4 mb-8">
    <div class="flex items-center gap-x-3">
        <input
            type="checkbox"
            wire:model.defer="state.accepted_terms"
            name="accepted_terms"
            id="accepted_terms"
            @error('accepted_terms')
                aria-invalid="true"
            @enderror
            class="border w-4 h-4 bg-white text-blue-600 border-gray-300 dark:bg-gray-700 dark:border-gray-600 rounded"
        />

        <label for="accepted_terms" class="text-sm text-gray-700 dark:text-white">
            <span>{{ __('easypost::labels.carrier_account_form.accepted_terms_label') }}</span>
            <a href="{{ $licenseUrl }}"
               target="_blank"
               rel="nofollow noreferrer"
               class="text-blue-500 underline"
            >
                {{ __('easypost::labels.carrier_account_form.accepted_terms_link') }}
            </a>
        </label>
    </div>

    @error ('accepted_terms')
        <div class="mt-1 text-sm text-red-500">
            {{ $message }}
        </div>
    @enderror
</div>
