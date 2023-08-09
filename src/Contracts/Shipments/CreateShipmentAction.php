<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Contracts\Shipments;

use CybrixSolutions\EasyPost\Contracts\Models\CarrierAccount;
use CybrixSolutions\EasyPost\Dto\PendingAddress;
use CybrixSolutions\EasyPost\Dto\PendingParcel;
use CybrixSolutions\EasyPost\Enums\DeliveryConfirmationEnum;
use CybrixSolutions\EasyPost\Enums\ShipmentDirectionEnum;
use CybrixSolutions\EasyPost\Enums\ShipmentOptions\LabelFormat;

interface CreateShipmentAction
{
    public function usingCarrierAccount(CarrierAccount $carrierAccount): self;

    public function usingDeliveryConfirmation(null|string|DeliveryConfirmationEnum $deliveryConfirmation): self;

    public function usingLabelFormat(string|LabelFormat $format): self;

    public function usingPendingParcel(PendingParcel $pendingParcel): self;

    public function usingReceiverAddress(array|PendingAddress $address): self;

    public function usingSenderAddress(array|PendingAddress $address): self;

    public function usingShipmentDirection(string|ShipmentDirectionEnum $shipmentDirection): self;

    public function usingShipmentOptions(array $options): self;

    public function usingShipmentMeta(array $meta): self;

    /** @return array<int, \EasyPost\Shipment> */
    public function execute(): array;
}
