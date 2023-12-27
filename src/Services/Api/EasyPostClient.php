<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Services\Api;

use CybrixSolutions\EasyPost\Tests\Fixtures\EasyPostMocks\EasyPostMock;
use EasyPost\EasyPostClient as Client;
use EasyPost\Test\Mocking\MockingUtility;

/**
 * @property \EasyPost\Service\AddressService $address
 * @property \EasyPost\Service\BatchService $batch
 * @property \EasyPost\Service\BillingService $billing
 * @property \EasyPost\Service\CarrierAccountService $carrierAccount
 * @property \EasyPost\Service\CustomsInfoService $customsInfo
 * @property \EasyPost\Service\CustomsItemService $customsItem
 * @property \EasyPost\Service\EndShipperService $endShipper
 * @property \EasyPost\Service\EventService $event
 * @property \EasyPost\Service\InsuranceService $insurance
 * @property \EasyPost\Service\OrderService $order
 * @property \EasyPost\Service\ParcelService $parcel
 * @property \EasyPost\Service\PickupService $pickup
 * @property \EasyPost\Service\RateService $rate
 * @property \EasyPost\Service\ReferralCustomerService $referralCustomer
 * @property \EasyPost\Service\RefundService $refund
 * @property \EasyPost\Service\ReportService $report
 * @property \EasyPost\Service\ScanFormService $scanForm
 * @property \EasyPost\Service\ShipmentService $shipment
 * @property \EasyPost\Service\TrackerService $tracker
 * @property \EasyPost\Service\UserService $user
 * @property \EasyPost\Service\WebhookService $webhook
 */
class EasyPostClient
{
    protected Client $client;

    protected static array $mockPaths = [];

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
        $basePath = __DIR__ . '/../../../vendor/easypost/easypost-php/test/EasyPost/Mocking';

        if (! isset(static::$mockPaths[$basePath])) {
            require_once "{$basePath}/MockingUtility.php";
            require_once "{$basePath}/MockRequest.php";
            require_once "{$basePath}/MockRequestMatchRule.php";
            require_once "{$basePath}/MockRequestResponseInfo.php";

            static::$mockPaths[$basePath] = true;
        }
    }
}
