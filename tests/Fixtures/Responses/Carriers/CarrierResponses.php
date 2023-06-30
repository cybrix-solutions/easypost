<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Tests\Fixtures\Responses\Carriers;

use EasyPost\EasyPostClient;
use EasyPost\EasyPostObject;

final readonly class CarrierResponses
{
    /**
     * @return array<int, \EasyPost\EasyPostObject>
     */
    public static function types(): array
    {
        return [
            self::betterTrucksAccount(),
            self::fedexAccount(),
            self::speedeeAccount(),
            self::upsAccount(),
        ];
    }

    public static function betterTrucksAccount(): EasyPostObject
    {
        $client = new EasyPostClient('key');

        $carrier = new EasyPostObject($client);
        $carrier->convertEach($client, [
            'object' => 'CarrierType',
            'type' => 'BetterTrucksAccount',
            'readable' => 'Better Trucks',
            'logo' => null,
            'fields' => [
                'credentials' => [
                    'api_key' => [
                        'visibility' => 'masked',
                        'label' => 'Better Trucks API key',
                    ],
                ],
                'test_credentials' => [
                    'api_key' => [
                        'visibility' => 'masked',
                        'label' => 'Test Better Trucks API key',
                    ],
                ],
            ],
        ]);

        return $carrier;
    }

    public static function fedexAccount(): EasyPostObject
    {
        $client = new EasyPostClient('key');

        $carrier = new EasyPostObject($client);
        $carrier->convertEach($client, [
            'object' => 'CarrierType',
            'type' => 'FedexAccount',
            'readable' => 'FedEx',
            'logo' => null,
            'fields' => [
                'credentials' => [],
                'creation_fields' => [
                    'credential_information' => [
                        'account_number' => [
                            'label' => 'FedEx Account #',
                            'visible' => 'visible',
                        ],
                    ],
                    'company_information' => [
                        'corporate_first_name' => [
                            'label' => 'Company Contact First Name',
                            'visible' => 'visible',
                        ],
                        'corporate_last_name' => [
                            'label' => 'Company Contact Last Name',
                            'visible' => 'visible',
                        ],
                        'corporate_job_title' => [
                            'label' => 'Company Contact Job Title',
                            'visible' => 'visible',
                        ],
                        'corporate_company_name' => [
                            'label' => 'Company Name',
                            'visible' => 'visible',
                        ],
                        'corporate_phone_number' => [
                            'label' => 'Company Phone',
                            'visible' => 'visible',
                        ],
                        'corporate_email_address' => [
                            'label' => 'Company Email',
                            'visible' => 'visible',
                        ],
                        'corporate_streets' => [
                            'label' => 'Company Street',
                            'visible' => 'visible',
                        ],
                        'corporate_city' => [
                            'label' => 'Company City',
                            'visible' => 'visible',
                        ],
                        'corporate_state' => [
                            'label' => 'Company State',
                            'visible' => 'visible',
                        ],
                        'corporate_postal_code' => [
                            'label' => 'Company Postal Code',
                            'visible' => 'visible',
                        ],
                        'corporate_country_code' => [
                            'label' => 'Company Country Code',
                            'visible' => 'visible',
                        ],
                    ],
                    'address_information' => [
                        'shipping_streets' => [
                            'label' => 'Shipping Street',
                            'visible' => 'visible',
                        ],
                        'shipping_city' => [
                            'label' => 'Shipping City',
                            'visible' => 'visible',
                        ],
                        'shipping_state' => [
                            'label' => 'Shipping State',
                            'visible' => 'visible',
                        ],
                        'shipping_postal_code' => [
                            'label' => 'Shipping Postal Code',
                            'visible' => 'visible',
                        ],
                        'shipping_country' => [
                            'label' => 'Shipping Country',
                            'visible' => 'visible',
                        ],
                    ],
                ],
                'custom_workflow' => true,
            ],
        ]);

        return $carrier;
    }

    public static function speedeeAccount(): EasyPostObject
    {
        $client = new EasyPostClient('key');

        $carrier = new EasyPostObject($client);
        $carrier->convertEach($client, [
            'object' => 'CarrierType',
            'type' => 'SpeedeeAccount',
            'readable' => 'Spee-Dee',
            'logo' => null,
            'fields' => [
                'credentials' => [
                    'account_number' => [
                        'visibility' => 'visible',
                        'label' => 'Spee-Dee Account Number',
                    ],
                    'ftp_username' => [
                        'visibility' => 'visible',
                        'label' => 'Spee-Dee FTP Username',
                    ],
                    'ftp_password' => [
                        'visibility' => 'password',
                        'label' => 'Spee-Dee FTP Password',
                    ],
                ],
            ],
        ]);

        return $carrier;
    }

    public static function upsAccount(): EasyPostObject
    {
        $client = new EasyPostClient('key');

        $carrier = new EasyPostObject($client);
        $carrier->convertEach($client, [
            'object' => 'CarrierType',
            'type' => 'UpsAccount',
            'readable' => 'UPS',
            'logo' => null,
            'fields' => [
                'credentials' => [],
                'creation_fields' => [
                    'registration_data' => [
                        'account_number' => [
                            'label' => 'UPS Account Number',
                            'visibility' => 'visible',
                        ],
                        'client_ip' => [
                            'label' => 'Client IP',
                            'visibility' => 'invisible',
                        ],
                        'name' => [
                            'label' => 'Company Contact Name',
                            'visibility' => 'visible',
                        ],
                        'title' => [
                            'label' => 'Company Contact Job Title',
                            'visibility' => 'visible',
                        ],
                        'company' => [
                            'label' => 'Company Name',
                            'visibility' => 'visible',
                        ],
                        'phone' => [
                            'label' => 'Company Phone',
                            'visibility' => 'visible',
                        ],
                        'email' => [
                            'label' => 'Company Email',
                            'visibility' => 'visible',
                        ],
                        'website' => [
                            'label' => 'Company Website',
                            'visibility' => 'visible',
                        ],
                        'street1' => [
                            'label' => 'Shipping Street 1',
                            'visibility' => 'visible',
                        ],
                        'street2' => [
                            'label' => 'Shipping Street 2',
                            'visibility' => 'visible',
                        ],
                        'city' => [
                            'label' => 'Shipping City',
                            'visibility' => 'visible',
                        ],
                        'state' => [
                            'label' => 'Shipping State',
                            'visibility' => 'visible',
                        ],
                        'postal_code' => [
                            'label' => 'Shipping Postal Code',
                            'visibility' => 'visible',
                        ],
                        'country' => [
                            'label' => 'Shipping Country',
                            'visibility' => 'visible',
                        ],
                        'invoice_number' => [
                            'label' => 'UPS Invoice Number',
                            'visibility' => 'visible',
                        ],
                        'invoice_date' => [
                            'label' => 'UPS Invoice Date',
                            'visibility' => 'visible',
                        ],
                        'invoice_amount' => [
                            'label' => 'UPS Invoice Amount',
                            'visibility' => 'visible',
                        ],
                        'invoice_currency' => [
                            'label' => 'UPS Invoice Currency',
                            'visibility' => 'visible',
                        ],
                        'invoice_control_id' => [
                            'label' => 'UPS Invoice Control ID',
                            'visibility' => 'visible',
                        ],
                    ],
                ],
                'custom_workflow' => true,
            ],
        ]);

        return $carrier;
    }
}
