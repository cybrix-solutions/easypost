<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CarrierFieldSection extends Component
{
    public function __construct(public string $title = '', public ?string $help = null)
    {
    }

    public function render(): View
    {
        return view($this->viewName());
    }

    protected function viewName(): string
    {
        return filled($this->help)
            ? 'easypost::components.partials.carrier-fields.carrier-field-section-with-help'
            : 'easypost::components.partials.carrier-fields.carrier-field-section';
    }
}
