<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Enums\ShipmentOptions;

/**
 * Available options for EasyPost shipments.
 *
 * @see https://www.easypost.com/docs/api/php#options
 */
enum ShipmentOption: string
{
    case AdditionalHandling = 'additional_handling';
    case AddressValidationLevel = 'address_validation_level';
    case Alcohol = 'alcohol';
    case ByDrone = 'by_drone';
    case CarbonNeutral = 'carbon_neutral';
    case CodAmount = 'cod_amount';
    case CodMethod = 'cod_method';
    case CodAddressId = 'cod_address_id';
    case Currency = 'currency';
    case DeliveryConfirmation = 'delivery_confirmation';
    case DeliveryMinDateTime = 'delivery_min_datetime';
    case DeliveryMaxDateTime = 'delivery_max_datetime';
    case DropOffType = 'dropoff_type';
    case DryIce = 'dry_ice';
    case DryIceMedical = 'dry_ice_medical';
    case DryIceWeight = 'dry_ice_weight';
    case DutyPayment = 'duty_payment';
    case Endorsement = 'endorsement';
    case EndShipperId = 'end_shipper_id';
    case FreightCharge = 'freight_charge';
    case HandlingInstructions = 'handling_instructions';
    case Hazmat = 'hazmat';
    case HoldForPickup = 'hold_for_pickup';
    case Incoterm = 'incoterm';
    case InvoiceNumber = 'invoice_number';
    case LabelDate = 'label_date';
    case LabelFormat = 'label_format';
    case Machinable = 'machinable';
    case Payment = 'payment';
    case PickupMinDateTime = 'pickup_min_datetime';
    case PickupMaxDateTime = 'pickup_max_datetime';
    case PrintCustom1 = 'print_custom_1';
    case PrintCustom2 = 'print_custom_2';
    case PrintCustom3 = 'print_custom_3';
    case PrintCustom1Barcode = 'print_custom_1_barcode';
    case PrintCustom2Barcode = 'print_custom_2_barcode';
    case PrintCustom3Barcode = 'print_custom_3_barcode';
    case PrintCustom1Code = 'print_custom_1_code';
    case PrintCustom2Code = 'print_custom_2_code';
    case PrintCustom3Code = 'print_custom_3_code';
    case SaturdayDelivery = 'saturday_delivery';
    case SpecialRatesEligibility = 'special_rates_eligibility';
    case SmartPostHub = 'smartpost_hub';
    case SmartPostManifest = 'smartpost_manifest';
    case BillingRef = 'billing_ref';
    case CertifiedMail = 'certified_mail';
    case RegisteredMail = 'registered_mail';
    case RegisteredMailAmount = 'registered_mail_amount';
    case ReturnReceipt = 'return_receipt'; // only applies to USPS

    // Deprecated options
    /** @deprecated Use the `payment` option object instead */
    case BillReceiverAccount = 'bill_receiver_account';

    /** @deprecated Use the `payment` option object instead */
    case BillReceiverPostalCode = 'bill_receiver_postal_code';

    /** @deprecated Use the `payment` option object instead */
    case BillThirdPartyAccount = 'bill_third_party_account';

    /** @deprecated Use the `payment` option object instead */
    case BillThirdPartyCountry = 'bill_third_party_country';

    /** @deprecated Use the `payment` option object instead */
    case BillThirdPartyPostalCode = 'bill_third_party_postal_code';
}
