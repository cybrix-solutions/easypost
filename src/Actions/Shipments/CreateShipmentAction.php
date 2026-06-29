<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Actions\Shipments;

use CybrixSolutions\EasyPost\Contracts\Models\CarrierAccount;
use CybrixSolutions\EasyPost\Contracts\Shipments\CreateShipmentAction as CreateShipmentActionContract;
use CybrixSolutions\EasyPost\Dto\PendingAddress;
use CybrixSolutions\EasyPost\Dto\PendingParcel;
use CybrixSolutions\EasyPost\Dto\ShipmentOptions;
use CybrixSolutions\EasyPost\Enums\DeliveryConfirmationEnum;
use CybrixSolutions\EasyPost\Enums\ShipmentDirectionEnum;
use CybrixSolutions\EasyPost\Enums\ShipmentOptions\LabelFormat;
use CybrixSolutions\EasyPost\Exceptions\Shipments\InvalidLabelFormat;
use CybrixSolutions\EasyPost\Services\ShipmentService;
use EasyPost\Shipment;

/**
 * Note: This action assumes you have validated your to/from addresses prior to
 * calling this action.
 *
 * This action WILL NOT purchase the shipment. That must be done using
 * the BuyShipment action.
 */
class CreateShipmentAction implements CreateShipmentActionContract
{
    protected CarrierAccount $carrierAccount;

    protected ?DeliveryConfirmationEnum $deliveryConfirmation = null;

    protected ShipmentDirectionEnum $shipmentDirection;

    protected string $labelFormat = 'PNG';

    protected PendingParcel $pendingParcel;

    protected PendingAddress $receiver;

    protected PendingAddress $sender;

    protected array $shipmentOptions = [];

    protected array $shipmentMeta = [];

    public function __construct(protected ShipmentService $api) {}

    public function usingCarrierAccount(CarrierAccount $carrierAccount): self
    {
        $this->carrierAccount = $carrierAccount;

        return $this;
    }

    public function usingDeliveryConfirmation(null|string|DeliveryConfirmationEnum $deliveryConfirmation): self
    {
        if (is_string($deliveryConfirmation)) {
            $deliveryConfirmation = DeliveryConfirmationEnum::tryFrom($deliveryConfirmation);
        }

        $this->deliveryConfirmation = $deliveryConfirmation;

        return $this;
    }

    public function usingLabelFormat(string|LabelFormat $format): self
    {
        $this->ensureValidLabelFormat($format);

        $this->labelFormat = $format instanceof LabelFormat
            ? $format->value
            : strtoupper($format);

        return $this;
    }

    public function usingPendingParcel(PendingParcel $pendingParcel): self
    {
        $this->pendingParcel = $pendingParcel;

        return $this;
    }

    public function usingReceiverAddress(array|PendingAddress $address): self
    {
        $this->receiver = $this->normalizeAddress($address);

        return $this;
    }

    public function usingSenderAddress(array|PendingAddress $address): self
    {
        $this->sender = $this->normalizeAddress($address);

        return $this;
    }

    public function usingShipmentDirection(string|ShipmentDirectionEnum $shipmentDirection): self
    {
        if (is_string($shipmentDirection)) {
            $shipmentDirection = ShipmentDirectionEnum::from($shipmentDirection);
        }

        $this->shipmentDirection = $shipmentDirection;

        return $this;
    }

    public function usingShipmentOptions(array $options): self
    {
        $this->shipmentOptions = $options;

        return $this;
    }

    public function usingShipmentMeta(array $meta): self
    {
        $this->shipmentMeta = $meta;

        return $this;
    }

    /**
     * @return array<int, Shipment>
     */
    public function execute(): array
    {
        $shipments = [$this->makeShipment(
            isReturn: $this->isReturnShipment(),
        )];

        if ($this->shouldGenerateReturnLabel()) {
            $shipments[] = $this->makeShipment(
                isReturn: true,
            );
        }

        return $shipments;
    }

    protected function makeShipment(bool $isReturn): Shipment
    {
        $options = ShipmentOptions::make()
            ->withDeliveryConfirmation($this->deliveryConfirmation)
            ->withLabelFormat($this->labelFormat)
            ->withPendingParcel($this->pendingParcel)
            ->withShipmentDirection($isReturn ? ShipmentDirectionEnum::ReturnLabel : $this->shipmentDirection)
            ->withOptions($this->shipmentOptions)
            ->toOptions();

        // EasyPost will reverse the from_address and to_address properties on return shipments for us.
        $data = [
            'from_address' => $this->sender->toShipmentArray(carrier: $this->carrierAccount->type, isSender: true),
            'to_address' => $this->receiver->toShipmentArray(carrier: $this->carrierAccount->type, isSender: false),
            'parcel' => $this->pendingParcel->toShipmentArray($isReturn),
            'options' => $options,
            'is_return' => $isReturn,
            'carrier_accounts' => [$this->carrierAccount->easypost_id],
            ...$this->shipmentMeta,
        ];

        return $this->api->create($data);
    }

    protected function isReturnShipment(): bool
    {
        return $this->shipmentDirection === ShipmentDirectionEnum::ReturnLabel;
    }

    protected function shouldGenerateReturnLabel(): bool
    {
        // You cannot generate return labels for a return shipment.
        if ($this->isReturnShipment()) {
            return false;
        }

        return $this->pendingParcel->needsReturnLabel();
    }

    protected function ensureValidLabelFormat(string|LabelFormat $format): void
    {
        if ($format instanceof LabelFormat) {
            return;
        }

        throw_unless(
            LabelFormat::isSupported($format),
            InvalidLabelFormat::invalid($format),
        );
    }

    protected function normalizeAddress(array|PendingAddress $address): PendingAddress
    {
        if ($address instanceof PendingAddress) {
            return $address;
        }

        return PendingAddress::make($address);
    }
}
