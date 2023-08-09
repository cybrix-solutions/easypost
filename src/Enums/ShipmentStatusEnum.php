<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Enums;

/**
 * @see https://www.easypost.com/what-do-your-tracking-statuses-mean
 */
enum ShipmentStatusEnum: string
{
    case PreTransit = 'pre_transit';
    case InTransit = 'in_transit';
    case OutForDelivery = 'out_for_delivery';
    case Delivered = 'delivered';
    case AvailableForPickup = 'available_for_pickup';
    case ReturnToSender = 'return_to_sender';
    case Failure = 'failure';
    case Cancelled = 'cancelled';
    case Error = 'error';
    case Unknown = 'unknown';

    public function label(): string
    {
        return __("easypost::enums.shipment_status.{$this->value}") ?? '';
    }

    public function description(): string
    {
        return __("easypost::enums.shipment_status.{$this->value}_description") ?? '';
    }

    public function color(): string
    {
        return match ($this) {
            self::Unknown, self::PreTransit, self::AvailableForPickup => 'orange',
            self::InTransit, self::OutForDelivery => 'blue',
            self::Delivered => 'green',
            default => 'red',
        };
    }

    public function isDelivered(): bool
    {
        return $this === self::Delivered;
    }

    public function isPickup(): bool
    {
        return $this === self::InTransit || $this === self::OutForDelivery;
    }

    public function isVoid(): bool
    {
        return $this === self::Cancelled;
    }

    public function isNotifiable(): bool
    {
        return in_array($this, config('easypost.notifiable_shipment_statuses', []), true);
    }
}
