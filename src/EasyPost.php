<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

final class EasyPost
{
    /**
     * The current version of the package.
     */
    public const VERSION = '0.1.3';

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
     * The callback that is responsible for retrieving the URL that EasyPost should send production webhooks to.
     *
     * @var callable|null
     */
    public static $resolveProductionWebhookUrlUsingCallback;

    /**
     * The callback that is responsible for retrieving the authenticated user's
     * ID.
     *
     * @var callable|null
     */
    public static $resolveAuthenticatedUserIdUsingCallback;

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
     * Retrieve the URL that EasyPost should send production webhooks to.
     */
    public function productionWebhookUrl(): string
    {
        return is_null(self::$resolveProductionWebhookUrlUsingCallback)
            ? Str::of($this->webhookProductionDomain() . '/' . $this->webhookProductionPath())
                ->lower()
                ->toString()
            : call_user_func(self::$resolveProductionWebhookUrlUsingCallback, $this->webhookProductionPath());
    }

    public function authenticatedUserId(): mixed
    {
        return is_null(self::$resolveAuthenticatedUserIdUsingCallback)
            ? Auth::id()
            : call_user_func(self::$resolveAuthenticatedUserIdUsingCallback);
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

    /**
     * Register a callback that is responsible for determining the production webhook url.
     */
    public static function resolveProductionWebhookUrlUsing(callable $callback): void
    {
        self::$resolveProductionWebhookUrlUsingCallback = $callback;
    }

    /**
     * Register a callback that is responsible for determining the authenticated user's ID.
     */
    public static function resolveAuthenticatedUserIdUsing(callable $callback): void
    {
        self::$resolveAuthenticatedUserIdUsingCallback = $callback;
    }

    /**
     * Retrieve the configured url domain to listen for webhooks at.
     */
    private function webhookProductionDomain(): string
    {
        return rtrim(config('app.url'), '/');
    }

    /**
     * Retrieve the configured url path to listen for webhooks at.
     */
    private function webhookProductionPath(): string
    {
        return Str::of(config('easypost.webhook_url'))
            ->ltrim('/')
            ->lower()
            ->toString();
    }
}
