<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Tests\Fixtures\Livewire;

use CybrixSolutions\EasyPost\Http\Livewire\Concerns\DeletesCarrierAccounts;
use CybrixSolutions\EasyPost\Http\Livewire\Concerns\ListsCarrierAccounts;
use CybrixSolutions\EasyPost\Http\Livewire\Concerns\ModifiesInternalCarrierAccountData;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class TestCarrierAccountList extends Component
{
    use AuthorizesRequests;
    use DeletesCarrierAccounts;
    use ListsCarrierAccounts;
    use ModifiesInternalCarrierAccountData;

    public function render(): string
    {
        return <<<'HTML'
        <ul>
            @foreach($this->rows as $row)
                <li wire:key="row{{ $row->id }}">{{ $row->name }}</li>
            @endforeach
        </ul>
        HTML;
    }
}
