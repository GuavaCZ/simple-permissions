<?php

namespace Guava\SimplePermissions\Casts;

use Guava\SimplePermissions\Facades\SimplePermissions;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class RoleCast implements CastsAttributes
{

    public function get(Model $model, string $key, mixed $value, array $attributes)
    {
        return new $value;
    }

    public function set(Model $model, string $key, mixed $value, array $attributes)
    {
        return is_string($value) ? $value : $value::class;
    }
}
