<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Actions\Shipments;

use CybrixSolutions\EasyPost\Contracts\Models\CarrierAccount;
use CybrixSolutions\EasyPost\Contracts\Models\Shipment as ShipmentModel;
use CybrixSolutions\EasyPost\Contracts\Shipments\BuyShipmentAction as BuyShipmentActionContract;
use CybrixSolutions\EasyPost\Enums\DeliveryConfirmationEnum;
use CybrixSolutions\EasyPost\Events\Shipments\ShipmentWasCreated;
use CybrixSolutions\EasyPost\Exceptions\Shipments\SelectedRateNotFound;
use CybrixSolutions\EasyPost\Services\ShipmentService;
use EasyPost\Rate;
use EasyPost\Shipment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BuyShipmentAction implements BuyShipmentActionContract
{
    protected CarrierAccount $carrierAccount;

    protected ?float $insurance = null;

    protected string $selectedRateId;

    protected Shipment $pendingShipment;

    protected ?Model $shippable = null;

    protected array $parcelMeta = [];

    protected array $meta = [];

    public function __construct(protected ShipmentService $api)
    {
    }

    public function usingCarrierAccount(CarrierAccount $carrierAccount): self
    {
        $this->carrierAccount = $carrierAccount;

        return $this;
    }

    public function usingInsurance(?float $insurance): self
    {
        $this->insurance = $insurance;

        return $this;
    }

    public function usingRate(string $rateId): self
    {
        $this->selectedRateId = $rateId;

        return $this;
    }

    public function usingShipment(Shipment $shipment): self
    {
        $this->pendingShipment = $shipment;

        return $this;
    }

    public function forShippable(?Model $model): self
    {
        $this->shippable = $model;

        return $this;
    }

    public function withParcelMeta(array $meta): self
    {
        $this->parcelMeta = $meta;

        return $this;
    }

    public function withShipmentMeta(array $meta): self
    {
        $this->meta = $meta;

        return $this;
    }

    public function execute(): ShipmentModel
    {
        $purchasedShipment = $this->buy();

        return DB::transaction(function () use ($purchasedShipment) {
            $shipment = $this->storeShipment($purchasedShipment);

            $this->associateParcel($shipment, $purchasedShipment);
            $this->addFeesToShipment($shipment, $purchasedShipment);

            ShipmentWasCreated::dispatch($shipment, $purchasedShipment);

            return $shipment;
        });
    }

    protected function buy(): Shipment
    {
        $rate = $this->getSelectedRate();

        return $this->api->buy($this->pendingShipment, $rate, $this->insurance);
    }

    protected function storeShipment(Shipment $purchasedShipment): ShipmentModel
    {
        return tap(app(ShipmentModel::class)::make(), function (ShipmentModel $shipment) use ($purchasedShipment) {
            $shipment->fill([
                'easypost_id' => $purchasedShipment->id,
                'carrier_account_id' => $this->carrierAccount->easypost_id,
                'carrier' => $this->carrierAccount->type->value,
                'tracking_number' => $purchasedShipment->tracking_code,
                'delivery_confirmation' => DeliveryConfirmationEnum::fromEasyPostValue($purchasedShipment->options->delivery_confirmation ?? null),
                'is_residential' => $purchasedShipment->to_address->residential ?? false,
                'is_return' => $purchasedShipment->is_return,
                'weight' => $purchasedShipment->parcel->weight,
                'status' => $purchasedShipment->status,

                'sender' => [
                    'name' => $purchasedShipment->from_address->company,
                    'attention' => $purchasedShipment->from_address->name,
                    'street' => trim("{$purchasedShipment->from_address->street1} {$purchasedShipment->from_address->street2}"),
                    'city' => $purchasedShipment->from_address->city,
                    'state' => $purchasedShipment->from_address->state,
                    'phone' => $purchasedShipment->from_address->phone,
                    'country' => $purchasedShipment->from_address->country,
                ],

                'receiver' => [
                    'name' => $purchasedShipment->to_address->company,
                    'attention' => $purchasedShipment->to_address->name,
                    'street' => trim("{$purchasedShipment->to_address->street1} {$purchasedShipment->to_address->street2}"),
                    'city' => $purchasedShipment->to_address->city,
                    'state' => $purchasedShipment->to_address->state,
                    'phone' => $purchasedShipment->to_address->phone,
                    'country' => $purchasedShipment->to_address->country,
                ],

                'easypost_label_url' => $purchasedShipment->postage_label->label_url,
                'tracker_url' => $purchasedShipment->tracker?->public_url,

                'shippable_id' => $this->shippable?->getKey(),
                'shippable_type' => $this->shippable?->getMorphClass(),

                ...$this->meta,
            ]);

            $shipment->save();
        });
    }

    protected function associateParcel(ShipmentModel $shipment, Shipment $purchasedShipment): void
    {
        $parcel = $purchasedShipment->parcel;

        $model = $shipment->parcels()->make([
            'easypost_id' => $parcel->id,
            'tracking_number' => $purchasedShipment->tracking_code,
            'tracker_id' => $purchasedShipment->tracker?->id,
            'tracker_url' => $purchasedShipment->tracker?->public_url,
            'easypost_label_id' => $purchasedShipment->postage_label->id,
            'easypost_label_url' => $purchasedShipment->postage_label->label_url,
            'status' => $purchasedShipment->status,
            'length' => $parcel->length,
            'width' => $parcel->width,
            'height' => $parcel->height,
            'weight' => $parcel->weight,
            'delivery_confirmation' => $shipment->delivery_confirmation,
            'value' => $purchasedShipment->insurance,
            'ref_number1' => $purchasedShipment->options->print_custom_1,
            'ref_number2' => $purchasedShipment->options->print_custoM_2,
            'is_return' => $shipment->is_return,

            ...$this->parcelMeta,
        ]);

        /**
         * We're calling "saveQuietly" to avoid any events from firing on the model to account for stuff like activity
         * logging or other things that might be listening to the model's events.
         *
         * However, we need to calculate the parcel's volume ourselves here since it listens for the saving event.
         */
        $model->adjustParcelDimensions();

        $model->saveQuietly();

        $shipment->setRelation('parcels', collect([$model]));
    }

    /**
     * @todo Associate all fees with shipment.
     */
    protected function addFeesToShipment(ShipmentModel $shipment, Shipment $purchasedShipment): void
    {
        if (! $purchasedShipment->selected_rate) {
            return;
        }

        $shipment->fill([
            'cost' => (float) $purchasedShipment->selected_rate->rate,
        ])->saveQuietly();
    }

    protected function getSelectedRate(): Rate
    {
        $rate = collect($this->pendingShipment->rates)
            ->filter(fn (Rate $rate) => $rate->id === $this->selectedRateId)
            ->first() ?? $this->pendingShipment->lowestRate();

        throw_unless(
            $rate,
            SelectedRateNotFound::make(),
        );

        return $rate;
    }
}
