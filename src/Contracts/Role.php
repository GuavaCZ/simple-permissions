<?php

namespace Guava\SimplePermissions\Contracts;

interface Role
{
    /**
     * @return Permission[]
     */
    public function permissions(): array;

    public function __toString(): string;
}
