<?php

namespace CybrixSolutions\EasyPost;

final class EasyPost
{
    /**
     * The current version of the package.
     */
    public const VERSION = '1.0.0';

    /**
     * The callback that is responsible for retrieving the configured EasyPost API key.
     *
     * @var callable|null
     */
    public static $resolveApiKeyUsingCallback;

    /**
     * The callback that is responsible for retrieving the configured EasyPost test API key.
     *
     * @var callable|null
     */
    public static $resolveTestApiKeyUsingCallback;

    /**
     * The callback that is responsible for determining if we are in test mode.
     *
     * @var callable|null
     */
    public static $resolveTestModeUsingCallback;

    /**
     * Retrieve the configured EasyPost API key.
     */
    public function apiKey(): ?string
    {
        return is_null(self::$resolveApiKeyUsingCallback)
            ? config('easypost.api_key')
            : call_user_func(self::$resolveApiKeyUsingCallback);
    }

    /**
     * Retrieve the configured EasyPost test API key.
     */
    public function testApiKey(): ?string
    {
        return is_null(self::$resolveTestApiKeyUsingCallback)
            ? config('easypost.test_api_key')
            : call_user_func(self::$resolveTestApiKeyUsingCallback);
    }

    /**
     * Determine if we are in test mode.
     */
    public function inTestMode(): bool
    {
        return is_null(self::$resolveTestModeUsingCallback)
            ? config('easypost.test_mode', false)
            : call_user_func(self::$resolveTestModeUsingCallback);
    }

    /**
     * Register a callback that is responsible for resolving the EasyPost API key.
     */
    public static function resolveApiKeyUsing(callable $callback): void
    {
        self::$resolveApiKeyUsingCallback = $callback;
    }

    /**
     * Register a callback that is responsible for resolving the EasyPost test API key.
     */
    public static function resolveTestApiKeyUsing(callable $callback): void
    {
        self::$resolveTestApiKeyUsingCallback = $callback;
    }

    /**
     * Register a callback that is responsible for determining if we are in test mode.
     */
    public static function resolveTestModeUsing(callable $callback): void
    {
        self::$resolveTestModeUsingCallback = $callback;
    }
}
