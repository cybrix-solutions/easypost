<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\View\Components;

use CybrixSolutions\EasyPost\Enums\CarrierEnum;
use CybrixSolutions\EasyPost\Exceptions\InvalidCarrierForCustomWorkflow;
use CybrixSolutions\EasyPost\Services\CarrierService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class CarrierAccountFormFields extends Component
{
    protected ?Collection $_customCredentials = null;

    public function __construct(
        public CarrierService $carrierService,
        public string $idPrefix = 'new_carrier_account',
        public bool $isCreate = true,
    ) {
    }

    public function render(): View
    {
        return view($this->viewName());
    }

    public function hasTestCredentials(): bool
    {
        return $this->carrierService->hasTestCredentials();
    }

    public function productionCredentials(): Collection
    {
        return $this->carrierService->productionCredentials();
    }

    public function testCredentials(): Collection
    {
        return $this->carrierService->testCredentials();
    }

    public function customCredentials(): Collection
    {
        return $this->_customCredentials ?? ($this->_customCredentials = $this->carrierService->customCredentials());
    }

    protected function viewName(): string
    {
        if (! $this->carrierService->isCustomWorkflow()) {
            return 'easypost::components.carrier-account-form-fields';
        }

        return match ($this->carrierService->carrierEnum()) {
            CarrierEnum::Fedex => 'easypost::components.partials.fedex-account-fields',
            CarrierEnum::Ups => 'easypost::components.partials.ups-account-fields',
            default => throw InvalidCarrierForCustomWorkflow::unsupported($this->carrierService->carrierEnum()->value),
        };
    }
}
