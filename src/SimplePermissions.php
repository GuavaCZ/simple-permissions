<?php

namespace Guava\SimplePermissions;

use Guava\SimplePermissions\Contracts\Permission;
use Guava\SimplePermissions\Contracts\Role;
use Guava\SimplePermissions\Models\Permissionable;
use Illuminate\Support\Collection;
use Spatie\StructureDiscoverer\Discover;

class SimplePermissions
{
    public function make(Permission $permission): string
    {
        return $permission::class . '.' . $permission->value;
    }

    public function getRoles(): Collection
    {
        return collect(Discover::in(app_path('Auth/Roles'))
            ->classes()
            ->implementing(Role::class)
            ->get())
            ->map(fn (string $role) => new $role)
        ;
    }

    public function getPermissions(): array
    {
        return collect($this->permissions())
            ->map(function (string | Permissionable $permission) {
                if ($permission instanceof Permissionable) {
                    return $permission;
                }

                return class_exists($permission)
                    ? $permission::cases()
                    : $permission;
            })
            ->flatten()
            ->map(fn (string | Permission $permission) => is_string($permission)
                ? $permission
                : $permission->getPermission())
            ->toArray()
        ;
    }

    public function permissionFromString(string $permission): ?Permission
    {
        if (! str($permission)->contains('.')) {
            return null;
        }

        [$permission, $value] = str($permission)->explode('.', 2);

        return $permission::tryFrom($value);
    }
}
