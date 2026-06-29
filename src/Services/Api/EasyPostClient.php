<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Services\Api;

use CybrixSolutions\EasyPost\Tests\Fixtures\EasyPostMocks\EasyPostMock;
use EasyPost\EasyPostClient as Client;
use EasyPost\Service\AddressService;
use EasyPost\Service\BatchService;
use EasyPost\Service\BillingService;
use EasyPost\Service\CarrierAccountService;
use EasyPost\Service\CustomsInfoService;
use EasyPost\Service\CustomsItemService;
use EasyPost\Service\EndShipperService;
use EasyPost\Service\EventService;
use EasyPost\Service\InsuranceService;
use EasyPost\Service\OrderService;
use EasyPost\Service\ParcelService;
use EasyPost\Service\PickupService;
use EasyPost\Service\RateService;
use EasyPost\Service\ReferralCustomerService;
use EasyPost\Service\RefundService;
use EasyPost\Service\ReportService;
use EasyPost\Service\ScanFormService;
use EasyPost\Service\ShipmentService;
use EasyPost\Service\TrackerService;
use EasyPost\Service\UserService;
use EasyPost\Service\WebhookService;
use EasyPost\Test\Mocking\MockingUtility;

/**
 * @property AddressService $address
 * @property BatchService $batch
 * @property BillingService $billing
 * @property CarrierAccountService $carrierAccount
 * @property CustomsInfoService $customsInfo
 * @property CustomsItemService $customsItem
 * @property EndShipperService $endShipper
 * @property EventService $event
 * @property InsuranceService $insurance
 * @property OrderService $order
 * @property ParcelService $parcel
 * @property PickupService $pickup
 * @property RateService $rate
 * @property ReferralCustomerService $referralCustomer
 * @property RefundService $refund
 * @property ReportService $report
 * @property ScanFormService $scanForm
 * @property ShipmentService $shipment
 * @property TrackerService $tracker
 * @property UserService $user
 * @property WebhookService $webhook
 */
class EasyPostClient
{
    protected Client $client;

    protected array $pendingMocks = [];

    public function __construct(string $apiKey)
    {
        $this->client = new Client($apiKey);
    }

    public function __get(string $name)
    {
        return $this->client->{$name};
    }

    public function setApiKey(string $apiKey): self
    {
        (fn () => $this->apiKey = $apiKey)->call($this->client);

        return $this;
    }

    // Testing Utils...
    public function addMock(EasyPostMock $mock): self
    {
        $this->ensureMockingUtilitiesAreLoaded();

        $this->pendingMocks[] = $mock->asMockRequest();

        return $this;
    }

    public function mock(): self
    {
        $this->client = new Client(
            apiKey: $this->client->getApiKey(),
            mockingUtility: new MockingUtility(...$this->pendingMocks),
        );

        $this->pendingMocks = [];

        return $this;
    }

    protected function ensureMockingUtilitiesAreLoaded(): void
    {
        //
    }
}
