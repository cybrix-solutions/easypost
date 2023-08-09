<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Enums\PrintCustomCodes;

enum UpsPrintCode: string
{
    case AccountsReceivableCustomerAccount = 'AJ';
    case AppropriationNumber = 'AT';
    case BillOfLadingNumber = 'BM';
    case CODNumber = '9V';
    case DealerOrderNumber = 'ON';
    case DepartmentNumber = 'DP';
    case FdaProductCode = '3Q';
    case InvoiceNumber = 'IK';
    case ManifestKeyNumber = 'MK';
    case ModelNumber = 'MJ';
    case PartNumber = 'PM';
    case ProductionCode = 'PC';
    case PurchaseOrderNumber = 'PO';
    case PurchaseRequestNumber = 'RQ';
    case ReturnAuthorizationNumber = 'RZ';
    case SalespersonNumber = 'SA';
    case SerialNumber = 'SE';
    case StoreNumber = 'ST';
    case TransactionReferenceNumber = 'TN';
    case EmployerIdNumber = 'EI';
    case FederalTaxpayerId = 'TJ';
}
