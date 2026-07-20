<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Tests\Fixtures\EasyPostMocks\Shipments;

use CybrixSolutions\EasyPost\Enums\TestTrackingCodes;
use CybrixSolutions\EasyPost\Tests\Fixtures\EasyPostMocks\EasyPostMock;

final class ShipmentMock extends EasyPostMock
{
    protected string $urlPattern = '/v2\\/shipments\\/\\S*$/';

    private string $id = 'shp_123';

    private bool $isReturn = false;

    private array $messages = [];

    private array $rates = [];

    private ?string $trackingCode = null;

    public function forCreation(): self
    {
        $this->method = 'post';
        $this->urlPattern = '/v2\\/shipments$/';

        return $this;
    }

    public function forId(string $id): self
    {
        if ($this->method !== 'post') {
            $this->urlPattern = '/v2\\/shipments\\/' . $id . '$/';
        }

        $this->id = $id;

        return $this;
    }

    public function isReturn(): self
    {
        $this->isReturn = true;

        return $this;
    }

    public function forTrackingCode(string $trackingCode): self
    {
        $this->trackingCode = $trackingCode;

        return $this;
    }

    public function withMessage(string $message): self
    {
        $this->messages[] = [
            'carrier' => 'UPS',
            'message' => $message,
            'type' => 'rate_error',
        ];

        return $this;
    }

    public function withRate(): self
    {
        $this->rates[] = [
            'id' => 'rate_123',
            'object' => 'Rate',
            'mode' => 'test',
            'service' => 'Ground',
            'carrier' => 'UPS',
            'rate' => '12.34',
            'currency' => 'USD',
            'retail_rate' => '12.34',
            'retail_currency' => 'USD',
            'list_rate' => '12.34',
            'list_currency' => 'USD',
            'billing_type' => 'carrier',
            'delivery_days' => 2,
            'delivery_date' => null,
            'delivery_date_guaranteed' => false,
            'est_delivery_days' => 2,
            'shipment_id' => $this->id,
            'carrier_account_id' => 'ca_123',
        ];

        return $this;
    }

    protected function getPayload(): array
    {
        return [
            'id' => $this->id,
            'object' => 'Shipment',
            'forms' => [],
            'fees' => [],
            'created_at' => '2023-07-10T17:30:31Z',
            'updated_at' => '2023-07-10T17:30:31Z',
            'is_return' => $this->isReturn,
            'mode' => 'test',
            'messages' => $this->messages,
            'rates' => $this->rates,
            'tracking_code' => $this->trackingCode ?? TestTrackingCodes::PreTransit->value,
            'from_address' => [
                'object' => 'Address',
                'id' => 'adr_123',
                'name' => 'EasyPost',
                'street1' => '417 Montgomery Street',
                'street2' => '5th Floor',
                'city' => 'San Francisco',
                'state' => 'CA',
                'zip' => '94104',
                'country' => 'US',
                'phone' => '4151234567',
                'email' => 'support@easypost.com',
                'mode' => 'test',
                'verifications' => [],
            ],
            'insurance' => null,
            'order_id' => null,
            'parcel' => [
                'object' => 'Parcel',
                'id' => 'prcl_123',
                'created_at' => '2023-07-10T17:30:31Z',
                'updated_at' => '2023-07-10T17:30:31Z',
                'length' => 20.2,
                'width' => 10.9,
                'height' => 5,
                'predefined_package' => null,
                'weight' => 65.9,
                'mode' => 'test',
            ],
            'to_address' => [
                'object' => 'Address',
                'id' => 'adr_321',
                'name' => 'Dr. Steve Brule',
                'company' => null,
                'street1' => '179 N Harbor Dr',
                'street2' => null,
                'city' => 'Redondo Beach',
                'state' => 'CA',
                'zip' => '90277',
                'country' => 'US',
                'phone' => '3108085243',
                'email' => 'dr_steve_brule@gmail.com',
                'mode' => 'test',
                'verifications' => [],
            ],
        ];
    }
}
