<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Tests\Fixtures\EasyPostMocks\CarrierAccounts;

use CybrixSolutions\EasyPost\Enums\CarrierEnum;
use CybrixSolutions\EasyPost\Tests\Fixtures\EasyPostMocks\EasyPostMock;
use Exception;

final class CarrierAccountMock extends EasyPostMock
{
    protected string $urlPattern = '/v2\\/carrier_accounts\\/\\S*$/';

    protected string $description = 'Mocked Account';

    protected ?string $reference = null;

    protected string $billingType = 'carrier';

    protected ?string $desiredId = null;

    protected CarrierEnum $type;

    protected static array $carrierPayloads = [
        CarrierEnum::Speedee->value => 'speedeePayload',
    ];

    public function __construct()
    {
        $this->type = CarrierEnum::Speedee;
    }

    public function urlPattern(): string
    {
        return $this->method === 'post'
            ? '/v2\\/carrier_accounts$/'
            : $this->urlPattern;
    }

    public function forId(string $id): self
    {
        // If the method is 'post', we're creating a new account.
        if ($this->method !== 'post') {
            $this->urlPattern = '/v2\\/carrier_accounts\\/' . $id . '$/';
        }

        $this->desiredId = $id;

        return $this;
    }

    public function usingDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function usingReference(?string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function withBillingType(string $billingType): self
    {
        $this->billingType = $billingType;

        return $this;
    }

    public function forAccountType(CarrierEnum $type): self
    {
        $this->type = $type;

        return $this;
    }

    protected function getPayload(): array
    {
        return [
            'id' => $this->desiredId ?? 'ca_2e0ee760dba9470a93176ef3bb84fcff',
            'object' => 'CarrierAccount',
            'type' => $this->type->value,
            'clone' => false,
            'created_at' => '2022-10-17T17:16:43Z',
            'updated_at' => '2022-10-17T17:16:43Z',
            'description' => $this->description,
            'reference' => $this->reference,
            'billing_type' => $this->billingType,
            'readable' => $this->type->label(),
            'logo' => null,
            'fields' => [
                ...$this->accountCredentials(),
            ],
        ];
    }

    protected function accountCredentials(): array
    {
        $methodName = self::$carrierPayloads[$this->type->value] ?? null;

        throw_unless($methodName, new Exception('No carrier payload implemented for ' . $this->type->value));

        return $this->{$methodName}();
    }

    protected function speedeePayload(): array
    {
        return [
            'credentials' => [
                'account_number' => [
                    'visibility' => 'visible',
                    'label' => 'Spee-Dee Account Number',
                    'value' => 'test',
                ],
                'ftp_username' => [
                    'visibility' => 'visible',
                    'label' => 'Spee-Dee FTP Username',
                    'value' => 'test',
                ],
                'ftp_password' => [
                    'visibility' => 'password',
                    'label' => 'Spee-Dee FTP Password',
                    'value' => '*******',
                ],
            ],
        ];
    }
}
