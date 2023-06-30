<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\View\Components;

use CybrixSolutions\EasyPost\Services\CarrierService;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CarrierServiceSignup extends Component
{
    public function __construct(public CarrierService $service)
    {
    }

    public function render(): View
    {
        return view('easypost::components.carrier-service-signup');
    }
}
