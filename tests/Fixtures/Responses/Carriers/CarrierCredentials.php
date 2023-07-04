<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Tests\Fixtures\Responses\Carriers;

use EasyPost\EasyPostClient;
use EasyPost\EasyPostObject;

final class CarrierCredentials
{
    public static function textCredential(): EasyPostObject
    {
        return self::newCredential([
            'visibility' => 'visible',
            'label' => 'Text Credential',
        ]);
    }

    public static function checkboxCredential(): EasyPostObject
    {
        return self::newCredential([
            'visibility' => 'checkbox',
            'label' => 'Checkbox Credential',
        ]);
    }

    public static function passwordCredential(): EasyPostObject
    {
        return self::newCredential([
            'visibility' => 'password',
            'label' => 'Password Credential',
        ]);
    }

    public static function readonlyCredential(): EasyPostObject
    {
        return self::newCredential([
            'visibility' => 'readonly',
            'label' => 'Readonly Credential',
        ]);
    }

    public static function selectCredential(): EasyPostObject
    {
        return self::newCredential([
            'visibility' => 'select',
            'label' => 'Select Credential',
        ]);
    }

    public static function optionalCredential(): EasyPostObject
    {
        return self::newCredential([
            'visibility' => 'visible',
            'label' => 'My Credential (optional)',
        ]);
    }

    protected static function newCredential(array $payload): EasyPostObject
    {
        $client = new EasyPostClient('key');

        $object = new EasyPostObject($client);
        $object->convertEach($client, $payload);

        return $object;
    }
}
