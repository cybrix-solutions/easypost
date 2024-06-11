<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Enums;

use CybrixSolutions\EasyPost\Exceptions\Shipments\InvalidDeliveryConfirmation;
use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasLabel;

enum DeliveryConfirmationEnum: string implements HasDescription, HasLabel
{
    // General...
    case None = 'none';
    case Signature = 'signature';
    case Adult = 'adult';

    // FedEx...
    case IndirectSignature = 'indirect_signature';

    // Gso...
    case GsoStandard = 'gso_standard_signature';

    // Usps...
    case AdultSignatureRestricted = 'adult_signature_restricted';
    case SignatureRestricted = 'signature_restricted';

    public static function fromEasyPostValue(?string $value): self
    {
        return match ($value) {
            null, 'NO_SIGNATURE' => self::None,
            'SIGNATURE' => self::Signature,
            'ADULT_SIGNATURE' => self::Adult,
            'INDIRECT_SIGNATURE' => self::IndirectSignature,
            'SIGNATURE_RESTRICTED' => self::SignatureRestricted,
            'ADULT_SIGNATURE_RESTRICTED' => self::AdultSignatureRestricted,
            'STANDARD_SIGNATURE' => self::GsoStandard,
            default => throw InvalidDeliveryConfirmation::fromEasyPostValue($value),
        };
    }

    public static function forCarrier(CarrierEnum $carrier): array
    {
        // AFAIK, these options should be available to all carrier types.
        $cases = [
            self::None,
            self::Signature,
            self::Adult,
        ];

        if ($carrier === CarrierEnum::Gso) {
            $cases[] = self::GsoStandard;
        }

        if ($carrier === CarrierEnum::Fedex) {
            $cases[] = self::IndirectSignature;
        }

        if ($carrier === CarrierEnum::Usps) {
            $cases[] = self::SignatureRestricted;
            $cases[] = self::AdultSignatureRestricted;
        }

        return $cases;
    }

    public function getLabel(): ?string
    {
        return __("easypost::enums.delivery_confirmation.{$this->value}");
    }

    public function description(): string
    {
        return __("easypost::enums.delivery_confirmation.{$this->value}_description");
    }

    public function easypostValue(): string
    {
        return match ($this) {
            self::None => 'NO_SIGNATURE',
            self::Signature => 'SIGNATURE',
            self::Adult => 'ADULT_SIGNATURE',
            self::GsoStandard => 'STANDARD_SIGNATURE',
            self::IndirectSignature => 'INDIRECT_SIGNATURE',
            self::SignatureRestricted => 'SIGNATURE_RESTRICTED',
            self::AdultSignatureRestricted => 'ADULT_SIGNATURE_RESTRICTED',
        };
    }

    public function getDescription(): ?string
    {
        return $this->description();
    }
}
