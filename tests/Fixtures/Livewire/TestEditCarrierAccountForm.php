<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Tests\Fixtures\Livewire;

use CybrixSolutions\EasyPost\Http\Livewire\Concerns\EditsCarrierAccount;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class TestEditCarrierAccountForm extends Component
{
    use AuthorizesRequests;
    use EditsCarrierAccount;

    public function render(): string
    {
        return <<<'HTML'
        @if ($this->show)
            <div id="edit-carrier-form">
                <x-easypost::carrier-account-form-fields
                    :carrier-service="$this->carrierService"
                />
            </div>
        @else
            <div>Button to show form</div>
        @endif
        HTML;
    }
}
