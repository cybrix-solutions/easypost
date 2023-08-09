<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Enums;

enum ShipmentRefundStatusEnum: string
{
    case Submitted = 'submitted';
    case Refunded = 'refunded';
    case Rejected = 'rejected';

    public function label(): string
    {
        return __("easypost::enums.shipment_refund_status.{$this->value}");
    }

    public function color(): string
    {
        return match ($this) {
            self::Submitted => 'blue',
            self::Refunded => 'green',
            self::Rejected => 'red',
        };
    }
}
