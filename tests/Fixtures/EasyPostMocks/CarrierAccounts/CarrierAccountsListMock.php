<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Tests\Fixtures\EasyPostMocks\CarrierAccounts;

use CybrixSolutions\EasyPost\Enums\CarrierEnum;
use CybrixSolutions\EasyPost\Tests\Fixtures\EasyPostMocks\EasyPostMock;

final class CarrierAccountsListMock extends EasyPostMock
{
    protected string $urlPattern = '/v2\\/carrier_accounts/';

    protected function getPayload(): array
    {
        return [
            $this->speedeeAccount(),
            $this->upsAccount(),
        ];
    }

    protected function speedeeAccount(): array
    {
        return [
            'id' => 'ca_speedee',
            'object' => 'CarrierAccount',
            'type' => 'SpeedeeAccount',
            'clone' => false,
            'created_at' => '2022-10-17T17:16:43Z',
            'updated_at' => '2022-10-17T17:16:43Z',
            'description' => 'Spee-Dee Mocked Account',
            'reference' => '',
            'billing_type' => 'carrier',
            'readable' => CarrierEnum::Speedee->label(),
            'logo' => null,
            'fields' => [
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
            ],
        ];
    }

    protected function upsAccount(): array
    {
        return [
            'id' => 'ca_ups',
            'object' => 'CarrierAccount',
            'type' => 'UpsAccount',
            'clone' => false,
            'created_at' => '2022-10-17T17:16:43Z',
            'updated_at' => '2022-10-17T17:16:43Z',
            'description' => 'UPS Mocked Account',
            'reference' => '',
            'billing_type' => 'carrier',
            'readable' => CarrierEnum::Ups->label(),
            'logo' => null,
            'fields' => [
                'credentials' => [
                    'account_number' => [
                        'visibility' => 'visible',
                        'label' => 'UPS Account Number',
                        'value' => 'test',
                    ],
                ],
            ],
        ];
    }
}
