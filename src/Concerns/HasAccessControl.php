<?php

namespace Guava\SimplePermissions\Concerns;

use Guava\SimplePermissions\Contracts\Permission;
use Guava\SimplePermissions\Facades\SimplePermissions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

trait HasAccessControl
{
    use HasPermissions {
        HasPermissions::hasPermission as hasPermissionViaModel;
    }
    use HasRoles {
        HasRoles::hasPermission as hasPermissionViaRole;
    }

    /**
     * Determine if the entity has the given abilities.
     *
     * @param  Permission|Permission[]|iterable|string  $abilities
     * @param  array|mixed  $arguments
     */
    public function can($abilities, $arguments = []): bool
    {
        return parent::can(
            abilities: array_map(fn ($ability) => $ability instanceof Permission
                ? SimplePermissions::make($ability)
                : $ability, Arr::wrap($abilities)),
            arguments: $arguments
        );
    }

    public function hasPermission(Permission $permission, ?Model $target = null): bool
    {
        if ($this->hasPermissionViaModel($permission, $target)) {
            return true;
        }

        return $this->hasPermissionViaRole($permission, $target);
    }
}
