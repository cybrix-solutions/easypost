<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Dto;

use CybrixSolutions\EasyPost\Enums\DeliveryConfirmationEnum;
use CybrixSolutions\EasyPost\Enums\ShipmentDirectionEnum;
use CybrixSolutions\EasyPost\Enums\ShipmentOptions\ShipmentOption;

/** @internal */
final class ShipmentOptions
{
    private ?DeliveryConfirmationEnum $deliveryConfirmation = null;

    private PendingParcel $pendingParcel;

    private ShipmentDirectionEnum $shipmentDirection;

    private ?string $labelFormat;

    private array $shipmentOptions = [];

    public static function make(): self
    {
        return new self;
    }

    public function withDeliveryConfirmation(?DeliveryConfirmationEnum $deliveryConfirmation): self
    {
        $this->deliveryConfirmation = $deliveryConfirmation;

        return $this;
    }

    public function withLabelFormat(?string $format): self
    {
        $this->labelFormat = $format;

        return $this;
    }

    public function withPendingParcel(PendingParcel $pendingParcel): self
    {
        $this->pendingParcel = $pendingParcel;

        return $this;
    }

    public function withShipmentDirection(ShipmentDirectionEnum $direction): self
    {
        $this->shipmentDirection = $direction;

        return $this;
    }

    public function withOptions(array $options): self
    {
        $this->shipmentOptions = $options;

        return $this;
    }

    public function toOptions(): array
    {
        $options = array_filter([
            ShipmentOption::DeliveryConfirmation->value => $this->deliveryConfirmation(),
            ShipmentOption::LabelFormat->value => $this->labelFormat,
        ]);

        // I've only ever gotten carriers like UPS to print the first barcode for a reference number, so we'll limit the print
        // barcode option to the first reference number we find.
        $printingBarcode = false;
        foreach (['1', '2', '3'] as $refPosition) {
            $refNumber = $this->refNumber($refPosition);
            if (! $refNumber) {
                continue;
            }

            $optionKey = match ($refPosition) {
                '1' => ShipmentOption::PrintCustom1->value,
                '2' => ShipmentOption::PrintCustom2->value,
                '3' => ShipmentOption::PrintCustom3->value,
            };

            $refNumber = htmlspecialchars($refNumber);

            $options[$optionKey] = $refNumber;

            if (! $printingBarcode && $this->pendingParcel->shouldPrintBarcodes()) {
                $barcodeKey = match ($refPosition) {
                    '1' => ShipmentOption::PrintCustom1Barcode->value,
                    '2' => ShipmentOption::PrintCustom2Barcode->value,
                    '3' => ShipmentOption::PrintCustom3Barcode->value,
                };

                $options[$barcodeKey] = true;
                $printingBarcode = true;
            }
        }

        // Any additional options that are provided are assumed to be valid EasyPost options
        // and will take priority over any options generated here.
        return array_merge($options, $this->shipmentOptions);
    }

    private function deliveryConfirmation(): ?string
    {
        // Delivery confirmation is not allowed for return labels.
        if ($this->isReturnShipment()) {
            return null;
        }

        return $this->deliveryConfirmation?->easypostValue() ?? DeliveryConfirmationEnum::None->easypostValue();
    }

    private function isReturnShipment(): bool
    {
        return $this->shipmentDirection === ShipmentDirectionEnum::ReturnLabel;
    }

    private function refNumber(string $position): ?string
    {
        if ($this->isReturnShipment()) {
            return $this->pendingParcel["return_ref_number{$position}"] ?? null;
        }

        return $this->pendingParcel["ref_number{$position}"] ?? null;
    }
}
