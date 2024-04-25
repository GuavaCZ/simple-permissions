<?php

namespace Guava\SimplePermissions\Concerns;

trait HasName
{

    public function getName(): string
    {
        return class_basename(static::class);
    }

    public function __toString(): string
    {
        return $this->getName();
    }
}
