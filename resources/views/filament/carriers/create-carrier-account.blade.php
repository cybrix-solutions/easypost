<div>

    {{-- carrier form --}}
    @if ($this->selectedCarrierType)
        <div>
            {{ $action->getModalAction('backToCarrierSearch') }}

            @includeWhen(
                $this->selectedCarrierService->signupUrl() || $this->selectedCarrierService->signupInstructions(),
                'easypost::filament.carriers.partials.carrier-signup'
            )

            <div class="mt-4 links">
                {{ $this->createCarrierForm }}
            </div>
        </div>
    @endif

    {{-- carriers list --}}
    @unless ($this->selectedCarrierType)
        <div>
            <div>
                {{ $this->carrierSearchForm }}
            </div>

            <x-easypost::filament.carriers.carrier-grid-list
                :carrier-types="\CybrixSolutions\EasyPost\Enums\CarrierEnum::fromSearch($this->carrierSearch ?? '')"
            />
        </div>
    @endunless

</div>
