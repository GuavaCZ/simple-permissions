<?php

namespace Guava\SimplePermissions\Filament\Concerns;

use Filament\Facades\Filament;
use Guava\SimplePermissions\Facades\SimplePermissions;
use Illuminate\Database\Eloquent\Model;

trait HasAuthorization
{
    public static function getPermissions(): string
    {
        return static::$permissions ?? str('App\\Auth\\Permissions\\')
            ->append(class_basename(static::getModel()))
            ->append('Permissions')
        ;
    }

    public static function can(string $action, ?Model $record = null): bool
    {
        if ($permission = static::getPermissions()::tryFrom($action)) {
//            dd($permission, static::isScopedToTenant() ? Filament::getTenant() : null);
            return Filament::auth()->user()->can($permission, [
                'target' => static::isScopedToTenant() ? Filament::getTenant() : null,
            ]);
        }

        return parent::can($action, $record);
    }
}
