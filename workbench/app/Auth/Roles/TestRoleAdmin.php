<?php

namespace Workbench\App\Auth\Roles;

use Guava\SimplePermissions\Contracts\Role;
use Workbench\App\Auth\Permissions\TestPermissions;

class TestRoleAdmin implements Role
{
    public function permissions(): array
    {
        return [
            ...TestPermissions::cases(),
        ];
    }
}
