<?php

namespace CybrixSolutions\EasyPost\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \CybrixSolutions\EasyPost\EasyPost
 */
class EasyPost extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \CybrixSolutions\EasyPost\EasyPost::class;
    }
}
