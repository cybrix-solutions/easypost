<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Enums\PrintCustomCodes;

enum FedexPrintCode: string
{
    case PurchaseOrderNumber = 'PO';
    case DepartmentNumber = 'DP';
    case ReturnMerchandiseAuthorization = 'RMA';
}
