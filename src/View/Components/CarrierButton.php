<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\View\Components;

use CybrixSolutions\EasyPost\Enums\CarrierEnum;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CarrierButton extends Component
{
    public function __construct(public CarrierEnum $carrier)
    {
    }

    public function render(): View
    {
        return view('easypost::components.carrier-button');
    }
}
