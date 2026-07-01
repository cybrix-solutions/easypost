<div>
    <x-filament::section
        :heading="__('easypost::livewire/carriers.accounts.heading')"
        :description="__('easypost::livewire/carriers.accounts.description')"
    >
        <x-slot:afterHeader>
            {{ $this->getAction('createCarrierAccount') }}
        </x-slot:afterHeader>

        {{ \Filament\Support\Facades\FilamentView::renderHook('easypost::carrier-account-manager.start') }}

        {{ $this->table }}
    </x-filament::section>

    <x-filament-actions::modals />
</div>
