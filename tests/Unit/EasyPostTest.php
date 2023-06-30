<?php

declare(strict_types=1);

use CybrixSolutions\EasyPost\EasyPost;

beforeEach(function () {
    $this->easypost = new EasyPost;
});

afterEach(function () {
    EasyPost::$resolveApiKeyUsingCallback = null;
    EasyPost::$resolveTestApiKeyUsingCallback = null;
    EasyPost::$resolveTestModeUsingCallback = null;
});

it('retrieves the configured api key', function () {
    EasyPost::$resolveApiKeyUsingCallback = null;

    config([
        'easypost.api_key' => 'foo',
    ]);

    expect($this->easypost->apiKey())->toBe('foo');
});

it('retrieves the configured test api key', function () {
    EasyPost::$resolveTestApiKeyUsingCallback = null;

    config([
        'easypost.test_api_key' => 'bar',
    ]);

    expect($this->easypost->testApiKey())->toBe('bar');
});

it('retrieves the configured test mode setting', function () {
    EasyPost::$resolveTestModeUsingCallback = null;

    config([
        'easypost.test_mode' => true,
    ]);

    expect($this->easypost->inTestMode())->toBeTrue();
});

it('can resolve the api key from a closure', function () {
    EasyPost::resolveApiKeyUsing(fn () => 'my resolved key');

    expect($this->easypost->apiKey())->toBe('my resolved key');
});

it('can resolve the test api key from a closure', function () {
    EasyPost::resolveTestApiKeyUsing(fn () => 'my resolved test key');

    expect($this->easypost->testApiKey())->toBe('my resolved test key');
});

it('can resolve the test mode setting from a closure', function () {
    EasyPost::resolveTestModeUsing(fn () => true);

    expect($this->easypost->inTestMode())->toBeTrue();
});
