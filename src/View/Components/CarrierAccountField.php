<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\View\Components;

use CybrixSolutions\EasyPost\Dto\EasyPostCredential;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CarrierAccountField extends Component
{
    public function __construct(
        public EasyPostCredential $credential,
        public string $credentialId,
        public string $namePrefix,
        public string $idPrefix,
        public bool $isTestEnv = false,
        public bool $isCreate = true,
    ) {
    }

    public function render(): View
    {
        return view('easypost::components.carrier-account-field', [
            'inputId' => $this->inputId(),
            'inputName' => $this->inputName(),
            'inputPartial' => $this->inputPartial(),
            'wireModelName' => $this->wireModel(),
        ]);
    }

    protected function inputPartial(): string
    {
        if ($this->credential->isCheckbox()) {
            return 'easypost::components.partials.carrier-fields.checkbox';
        }

        if ($this->credential->isSelect()) {
            return 'easypost::components.partials.carrier-fields.select';
        }

        return 'easypost::components.partials.carrier-fields.input';
    }

    protected function inputId(): string
    {
        return "{$this->idPrefix}.{$this->namePrefix}.{$this->credentialId}";
    }

    protected function inputName(): string
    {
        return "{$this->namePrefix}.{$this->credentialId}";
    }

    protected function wireModel(): string
    {
        return "state.{$this->namePrefix}.{$this->credentialId}";
    }
}
