<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Enums\ShipmentOptions;

enum EndorsementType: string
{
    case AddressServiceRequested = 'ADDRESS_SERVICE_REQUESTED';
    case ForwardingServiceRequested = 'FORWARDING_SERVICE_REQUESTED';
    case ChangeServiceRequested = 'CHANGE_SERVICE_REQUESTED';
    case ReturnServiceRequested = 'RETURN_SERVICE_REQUESTED';
    case LeaveIfNoResponse = 'LEAVE_IF_NO_RESPONSE';
}
