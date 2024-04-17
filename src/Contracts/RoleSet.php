<?php

namespace Guava\SimplePermissions\Contracts;

interface RoleSet
{
    /**
     * @return Role[]
     */
    public function roles(): array;
}
