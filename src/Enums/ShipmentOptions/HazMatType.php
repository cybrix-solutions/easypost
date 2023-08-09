<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Enums\ShipmentOptions;

/**
 * @see https://www.easypost.com/usps-guide#hazardous-materials-hazmat
 * @see https://pe.usps.com/text/pub52/welcome.htm
 */
enum HazMatType: string
{
    // FedEx and DHL eCommerce
    case PrimaryContained = 'PRIMARY_CONTAINED';
    case PrimaryPacked = 'PRIMARY_PACKED';
    case Primary = 'PRIMARY';
    case SecondaryContained = 'SECONDARY_CONTAINED';
    case SecondaryPacked = 'SECONDARY_PACKED';
    case Secondary = 'SECONDARY';
    case OtherRegulatedMaterialsDomestic = 'ORMD';
    case Lithium = 'LITHIUM';

    // FedEx, DHL eCommerce, and USPS
    case LimitedQuantityGroundPackage = 'LIMITED_QUANTITY';

    // USPS
    case AirEligibleEthanol = 'AIR_ELIGIBLE_ETHANOL';
    case Class1 = 'CLASS_1'; // Toy Propellant/Safe Fuse Package
    case Class3 = 'CLASS_3';
    case Class7 = 'CLASS_7'; // Radioactive Materials Package
    case Class8Corrosive = 'CLASS_8_CORROSIVE';
    case Class8WetBattery = 'CLASS_8_WET_BATTERY';
    case Class9NewLithiumIndividual = 'CLASS_9_NEW_LITHIUM_INDIVIDUAL';
    case Class9UsedLithium = 'CLASS_9_USED_LITHIUM';
    case Class9NewLithiumDevice = 'CLASS_9_NEW_LITHIUM_DEVICE';
    case Class9DryIce = 'CLASS_9_DRY_ICE';
    case Class9UnmarkedLithium = 'CLASS_9_UNMARKED_LITHIUM';
    case Class9Magnetized = 'CLASS_9_MAGNETIZED';
    case Division4_1 = 'DIVISION_4_1'; // Division 4.1 Mailable flammable solids and Safety Matches Package
    case Division5_1 = 'DIVISION_5_1'; // Division 5.1 Oxidizer Package
    case Division5_2 = 'DIVISION_5_2'; // Division 5.2 Organic Peroxides Package
    case Division6_1 = 'DIVISION_6_1'; // Division 6.1 Toxic Materials Package (with an LD50 of 50 mg/kg or less)
    case Division6_2 = 'DIVISION_6_2'; // Division 6.2
    case ExceptedQuantityProvision = 'EXCEPTED_QUANTITY_PROVISION';
    case GroundOnly = 'GROUND_ONLY';
    case ID8000 = 'ID8000';
    case Lighters = 'LIGHTERS';
    case SmallQuantityProvision = 'SMALL_QUANTITY_PROVISION';
}
