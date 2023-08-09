<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Enums\ShipmentOptions;

enum Incoterm: string
{
    case EXW = 'EXW';
    case FCA = 'FCA';
    case CPT = 'CPT';
    case CIP = 'CIP';
    case DAT = 'DAT';
    case DAP = 'DAP';
    case DDP = 'DDP';
    case FAS = 'FAS';
    case FOB = 'FOB';
    case CFR = 'CFR';
    case CIF = 'CIF';
}
