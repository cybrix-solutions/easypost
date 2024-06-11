<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ShipmentRefundStatusEnum: string implements HasColor, HasLabel
{
    case Submitted = 'submitted';
    case Refunded = 'refunded';
    case Rejected = 'rejected';

    public function getLabel(): ?string
    {
        return __("easypost::enums.shipment_refund_status.{$this->value}");
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Submitted => 'primary',
            self::Refunded => 'success',
            self::Rejected => 'danger',
        };
    }
}
