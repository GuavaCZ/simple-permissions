<?php

namespace Guava\SimplePermissions\Concerns;

use Guava\SimplePermissions\Contracts\Permission;
use Guava\SimplePermissions\Contracts\Role;
use Guava\SimplePermissions\Facades\SimplePermissions;
use Illuminate\Support\Arr;

trait HasAccessControl
{
    use HasPermissions;
    use HasRoles;

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

    public function hasPermission(string $permission): bool
    {
        $permission = SimplePermissions::permissionFromString($permission);

        if (in_array($permission, $this->permissions->toArray())) {
            return true;
        }

        return (bool) $this->roles->first(
            fn (Role $role) => in_array($permission, $role->permissions()),
        );
    }
}
