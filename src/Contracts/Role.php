<?php

namespace Guava\SimplePermissions\Contracts;

interface Role
{
    //    public function id(): string;

    /**
     * @return Permission[]
     */
    public function permissions(): array;
}
