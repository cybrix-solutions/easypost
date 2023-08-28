<?php

declare(strict_types=1);

use CybrixSolutions\EasyPost\EasyPost;

use function CybrixSolutions\EasyPost\carrierAccountCacheKey;
use function CybrixSolutions\EasyPost\hasApiKey;

it('can get the cache key for a given carrier account', function () {
    config()->set([
        'easypost.cache.carrier_account.key' => 'easypost::carrier_account.{account}',
    ]);

    expect(carrierAccountCacheKey('ca_123'))->toBe('easypost::carrier_account.ca_123');
});

it('can determine if a production api key is set', function () {
    EasyPost::$resolveApiKeyUsingCallback = null;

    config([
        'easypost.api_key' => '',
    ]);

    expect(hasApiKey())->toBeFalse();

    config([
        'easypost.api_key' => 'my_api_key',
    ]);

    expect(hasApiKey())->toBeTrue();
});
