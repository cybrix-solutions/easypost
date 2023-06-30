<input
    type="{{ $credential->isPassword() ? 'password' : 'text' }}"
    wire:model.defer="{{ $wireModelName }}"
    name="{{ $inputName }}"
    id="{{ $inputId }}"
    placeholder="{{ $credential->placeholder() }}"
    @if (! $isTestEnv && $credential->isRequired()) required @endif
    class="block w-full border shadow-none sm:text-sm bg-white py-2.5 px-3 rounded-lg border-gray-300 text-gray-900 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
    @error($inputName)
        aria-invalid="true"
    @enderror
/>
