<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost;

use CybrixSolutions\EasyPost\Facades\EasyPost;
use EasyPost\EasyPostObject;
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

/**
 * Something changed in v7 of the EasyPost package that is causing their objects
 * not to serialize properly, so we'll convert them to an array to work with
 * cache storage.
 */
function easypostObjectToArray(EasyPostObject $obj): array
{
    $array = $obj->__toArray();

    foreach ($array as $key => $value) {
        if ($value instanceof EasyPostObject) {
            $array[$key] = easypostObjectToArray($value);
        }
    }

    return $array;
}

function setLinkTargets(string $html, string $target = '_blank'): string
{
    $pattern = '/<a(.*?)>/i';

    $rel = $target === '_blank' ? ' rel="noopener nofollow external"' : '';

    return preg_replace($pattern, '<a$1 target="' . $target . '"' . $rel . '>', $html);
}
