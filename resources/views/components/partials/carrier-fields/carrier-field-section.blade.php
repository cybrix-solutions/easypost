<fieldset {{ $attributes->except('title') }}>
    <legend class="text-gray-900 dark:text-white text-lg border-b border-gray-200 dark:border-gray-500 w-full pb-1 mb-5">
        {{ $title }}
    </legend>

    {{ $slot }}
</fieldset>
