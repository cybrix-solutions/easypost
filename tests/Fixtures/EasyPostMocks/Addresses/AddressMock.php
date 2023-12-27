<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Tests\Fixtures\EasyPostMocks\Addresses;

use CybrixSolutions\EasyPost\Tests\Fixtures\EasyPostMocks\EasyPostMock;

final class AddressMock extends EasyPostMock
{
    protected string $urlPattern = '/v2\\/addresses$/';

    protected string $method = 'post';

    private string $mode = 'create';

    private ?string $id = null;

    private ?string $name = null;

    private ?string $company = null;

    private ?string $street1 = '417 MONTGOMERY ST';

    private ?string $street2 = 'FlOOR 5';

    private ?string $city = 'SAN FRANCISCO';

    private ?string $state = 'CA';

    private ?string $zip = '94104';

    private ?string $country = 'US';

    private ?array $errors = null;

    public function createAndVerify(): self
    {
        $this->mode = 'create_and_verify';

        return $this;
    }

    public function verifyStrict(): self
    {
        $this->mode = 'verify_strict';
        $this->statusCode = 'ADDRESS.VERIFY.FAILURE';

        return $this;
    }

    public function forId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function forName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function forCompany(string $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function forStreet1(?string $street): self
    {
        $this->street1 = $street;

        return $this;
    }

    public function forStreet2(?string $street): self
    {
        $this->street2 = $street;

        return $this;
    }

    public function forCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function forState(?string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function forZip(?string $zip): self
    {
        $this->zip = $zip;

        return $this;
    }

    public function forCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function withErrors(array $errors): self
    {
        $this->errors = $errors;

        return $this;
    }

    protected function getPayload(): array
    {
        if ($this->mode === 'verify_strict') {
            return [
                'error' => [
                    'code' => $this->statusCode,
                    'message' => 'Unable to verify address',
                    'errors' => [
                        [
                            'code' => 'E.ADDRESS.NOT_FOUND',
                            'field' => 'address',
                            'message' => 'Address not found.',
                            'suggestion' => null,
                        ],
                        [
                            'code' => 'E.HOUSE_NUMBER.MISSING',
                            'field' => 'street1',
                            'message' => 'House number is missing',
                            'suggestion' => null,
                        ],
                    ],
                ],
            ];
        }

        return [
            'id' => $this->id ?? 'adr_5dacbea44e3f11ed90f5ac1f6bc72124',
            'object' => 'Address',
            'created_at' => '2022-10-17T17:15:57+00:00',
            'updated_at' => '2022-10-17T17:15:57+00:00',
            'name' => $this->name,
            'company' => $this->company ?? 'EasyPost',
            'street1' => $this->street1,
            'street2' => $this->street2,
            'city' => $this->city,
            'state' => $this->state,
            'zip' => $this->zip,
            'country' => $this->country,
            'phone' => '4151234567',
            'email' => null,
            'mode' => 'test',
            'carrier_facility' => null,
            'residential' => null,
            'federal_tax_id' => null,
            'state_tax_id' => null,
            'verifications' => $this->verifications(),
        ];
    }

    private function verifications(): array
    {
        if ($this->mode === 'create') {
            return [];
        }

        $zip4 = $this->baseVerificationObject();
        $delivery = $this->baseVerificationObject();

        if (is_null($this->errors)) {
            return [
                'object' => 'Verifications',
                'zip4' => $zip4,
                'delivery' => $delivery,
            ];
        }

        $delivery['success'] = false;
        $zip4['success'] = false;

        $errors = collect($this->errors)
            ->map(function (array $error) {
                return array_merge($error, [
                    'object' => 'FieldError',
                ]);
            })
            ->toArray();

        $zip4['errors'] = $errors;
        $delivery['errors'] = $errors;

        return [
            'object' => 'Verifications',
            'zip4' => $zip4,
            'delivery' => $delivery,
        ];
    }

    private function baseVerificationObject(): array
    {
        return [
            'success' => true,
            'errors' => [],
            'details' => null,
            'object' => 'Verification',
        ];
    }
}
