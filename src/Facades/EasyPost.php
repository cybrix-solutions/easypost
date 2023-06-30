<?php

namespace CybrixSolutions\EasyPost\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string|null apiKey()
 * @method static string|null testApiKey()
 * @method static bool inTestMode()
 *
 * @see \CybrixSolutions\EasyPost\EasyPost
 */
class EasyPost extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \CybrixSolutions\EasyPost\EasyPost::class;
    }
}
