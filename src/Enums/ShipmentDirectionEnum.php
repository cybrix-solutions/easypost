<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Enums;

enum ShipmentDirectionEnum: string
{
    case Forward = 'forward';
    case ReturnLabel = 'return';

    public function color(): string
    {
        return match ($this) {
            self::Forward => 'green',
            self::ReturnLabel => 'blue',
        };
    }

    public function label(): string
    {
        return __("easypost::enums.shipment_direction.{$this->value}");
    }

    public function description(): string
    {
        return __("easypost::enums.shipment_direction.{$this->value}_description");
    }
}
