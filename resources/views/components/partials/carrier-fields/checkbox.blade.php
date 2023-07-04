<div class="flex items-center gap-x-3">
    <input
        type="checkbox"
        wire:model.defer="{{ $wireModelName }}"
        name="{{ $inputName }}"
        id="{{ $inputId }}"
        @error($inputName)
        aria-invalid="true"
        @enderror
        class="border w-4 h-4 bg-white text-blue-600 border-gray-300 dark:bg-gray-700 dark:border-gray-600 rounded"
        @if ($credential->isReadonly()) readonly @endif
    />

    <label for="{{ $inputId }}" class="text-sm text-gray-700 dark:text-white">{{ $credential->label() }}</label>
</div>
