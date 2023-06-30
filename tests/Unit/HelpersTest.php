<?php

declare(strict_types=1);

use CybrixSolutions\EasyPost\EasyPost;
use function CybrixSolutions\EasyPost\hasApiKey;

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
