<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost;

use CybrixSolutions\EasyPost\Facades\EasyPost;
use Illuminate\Support\Str;

/**
 * Get the cache key for a carrier account.
 */
function carrierAccountCacheKey(string $accountId): string
{
    return Str::replace(
        '{account}',
        $accountId,
        config('easypost.cache.carrier_account.key'),
    );
}

/**
 * Determine if a production EasyPost API key is set.
 */
function hasApiKey(): bool
{
    return filled(EasyPost::apiKey());
}
