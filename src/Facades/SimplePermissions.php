<?php

namespace Guava\SimplePermissions\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Guava\SimplePermissions\SimplePermissions
 */
class SimplePermissions extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Guava\SimplePermissions\SimplePermissions::class;
    }
}
