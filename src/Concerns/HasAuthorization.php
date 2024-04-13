<?php

namespace Guava\SimplePermissions\Concerns;

use Filament\Facades\Filament;
use Guava\SimplePermissions\Facades\SimplePermissions;
use Illuminate\Database\Eloquent\Model;

trait HasAuthorization
{
    public static function getPermissions(): string
    {
        return static::$permissions ?? str('App\\Auth\\Permissions\\')
            ->append(class_basename(static::getModel()))
            ->append('Permissions');
    }

    public static function can(string $action, ?Model $record = null): bool
    {
        if ($permission = static::getPermissions()::tryFrom($action)) {
            return Filament::auth()->user()->can(SimplePermissions::make($permission));
        }

        return parent::can($action, $record);
    }
}
