<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Enums;

enum TestTrackingCodes: string
{
    case PreTransit = 'EZ1000000001';
    case InTransit = 'EZ2000000002';
    case OutForDelivery = 'EZ3000000003';
    case Delivered = 'EZ4000000004';
    case ReturnToSender = 'EZ5000000005';
    case Failure = 'EZ6000000006';
    case Unknown = 'EZ7000000007';
}
