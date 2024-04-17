<?php

namespace Guava\SimplePermissions\Casts;

use Guava\SimplePermissions\Facades\SimplePermissions;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class PermissionCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes)
    {
        return SimplePermissions::permissionFromString($value);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes)
    {
        return SimplePermissions::make($value);
    }
}
