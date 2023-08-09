<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Contracts\Shipments;

use CybrixSolutions\EasyPost\Contracts\Models\CarrierAccount;
use CybrixSolutions\EasyPost\Contracts\Models\Shipment as ShipmentModel;
use EasyPost\Shipment;
use Illuminate\Database\Eloquent\Model;

interface BuyShipmentAction
{
    public function usingCarrierAccount(CarrierAccount $carrierAccount): self;

    public function usingInsurance(?float $insurance): self;

    public function usingRate(string $rateId): self;

    public function usingShipment(Shipment $shipment): self;

    public function forShippable(?Model $model): self;

    public function withParcelMeta(array $meta): self;

    public function withShipmentMeta(array $meta): self;

    public function execute(): ShipmentModel;
}
