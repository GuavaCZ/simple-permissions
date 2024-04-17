<?php

namespace Workbench\App\Auth\Roles;

use Guava\SimplePermissions\Contracts\Role;
use Workbench\App\Auth\Permissions\TestPermissions;

class TestRoleUser implements Role
{
    public function permissions(): array
    {
        return [
            TestPermissions::VIEW,
            TestPermissions::DELETE,
        ];
    }
}
