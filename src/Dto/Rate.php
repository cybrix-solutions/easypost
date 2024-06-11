<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Dto;

use EasyPost\Rate as EasyPostRate;
use Illuminate\Contracts\Support\Arrayable;

class Rate implements Arrayable
{
    public function __construct(protected EasyPostRate $rate)
    {
    }

    public static function fromRate(EasyPostRate $rate): static
    {
        return new static($rate);
    }

    public function getRate(): EasyPostRate
    {
        return $this->rate;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->rate->id,
            'service' => $this->rate->service,
            'carrier' => $this->rate->carrier,
            'rate' => $this->rate->rate,
            'currency' => $this->rate->currency,
            'retail_rate' => $this->rate->retail_rate,
            'list_rate' => $this->rate->list_rate,
            'billing_type' => $this->rate->billing_type ?? 'carrier',
            'delivery_days' => $this->rate->delivery_days,
            'delivery_date' => $this->rate->delivery_date,
            'est_delivery_days' => $this->rate->est_delivery_days ?? $this->rate->delivery_days,
            'shipment_id' => $this->rate->shipment_id,
            'carrier_account_id' => $this->rate->carrier_account_id,
        ];
    }
}
