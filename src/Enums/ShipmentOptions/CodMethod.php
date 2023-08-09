<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Enums\ShipmentOptions;

enum CodMethod: string
{
    case Cash = 'CASH';
    case Check = 'CHECK';
    case MoneyOrder = 'MONEY_ORDER';
}
