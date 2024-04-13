<?php

namespace Guava\SimplePermissions;

use Guava\SimplePermissions\Contracts\Permission;
use Guava\SimplePermissions\Contracts\Role;
use Spatie\StructureDiscoverer\Discover;

class SimplePermissions
{

    public function make(Permission $permission): string
    {
        return $permission::class .'.'.$permission->value;
    }

    public function getRoles(): array
    {
        return Discover::in(app_path('Auth/Roles'))
            ->classes()
            ->implementing(Role::class)
            ->get();
    }

    public function getPermissions(): array
    {
        return collect($this->permissions())
            ->map(function (string|Permissionable $permission) {
                if ($permission instanceof Permissionable) {
                    return $permission;
                }

                return class_exists($permission)
                    ? $permission::cases()
                    : $permission;
            })
            ->flatten()
            ->map(fn(string|Permission $permission) => is_string($permission)
                ? $permission
                : $permission->getPermission())
            ->toArray();
    }

    public function permissionFromString(string $permission): ?Permission
    {
        [$permission, $value] = str($permission)->explode('.', 2);
        return $permission::tryFrom($value);
    }
}
