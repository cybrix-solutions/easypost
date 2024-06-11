<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasLabel;

enum ShipmentDirectionEnum: string implements HasColor, HasDescription, HasLabel
{
    case Forward = 'forward';
    case ReturnLabel = 'return';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Forward => 'success',
            self::ReturnLabel => 'primary',
        };
    }

    public function getLabel(): string
    {
        return __("easypost::enums.shipment_direction.{$this->value}");
    }

    public function description(): string
    {
        return __("easypost::enums.shipment_direction.{$this->value}_description");
    }

    public function getDescription(): ?string
    {
        return $this->description();
    }
}
