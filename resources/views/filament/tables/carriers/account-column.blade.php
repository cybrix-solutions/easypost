@php
    $record = $getRecord();
@endphp

<div>
    <div class="flex gap-x-4 py-2">
        <div
            class="w-[6rem] bg-no-repeat bg-[50%] bg-contain shrink-0 self-stretch"
            style="background-image: url(@js($record->type->image()));"
        >
        </div>

        <div class="fi-ta-text flex-auto grid w-full gap-y-1 px-3 py-4">
            <div class="flex">
                <div class="flex max-w-max">
                    <div class="fi-ta-text-item inline-flex items-center gap-1.5">
                        <span class="fi-ta-text-item-label text-sm leading-6 text-gray-950 dark:text-white">
                            {{ $record->name }}
                        </span>
                    </div>
                </div>
            </div>

            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ __('easypost::livewire/carriers.accounts.table.easypost_id.description', ['id' => $record->easypost_id]) }}
            </p>

            @if ($record->isEasyPostAccount())
                <p class="text-xs leading-5 text-gray-500 dark:text-gray-400 italic">
                    {{ __('easypost::labels.carrier_account.is_easypost_account') }}
                </p>
            @endif
        </div>
    </div>
</div>
