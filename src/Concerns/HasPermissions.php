<?php

namespace Guava\SimplePermissions\Concerns;

use Guava\SimplePermissions\Contracts\Permission;
use Guava\SimplePermissions\Contracts\Role;
use Guava\SimplePermissions\Facades\SimplePermissions;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

trait HasPermissions
{
    public function permissions(): Attribute
    {
        return Attribute::make(
            get: function () {
                [$type, $id] = $this->getMorphs('permissionable', null, null);

                return DB::table(config('simple-permissions.tables.permissions'))
                    ->where($type, static::class)
                    ->where($id, $this->getKey())
                    ->pluck('permission')
                    ->map(fn ($permission) => SimplePermissions::permissionFromString($permission))
                ;
            },
            set: function ($value) {
                [$type, $id] = $this->getMorphs('permissionable', null, null);

                $oldValues = DB::table(config('simple-permissions.tables.permissions'))
                    ->where($type, static::class)
                    ->where($id, $this->getKey())
                    ->pluck('permission')
                    ->map(fn ($permission) => SimplePermissions::permissionFromString($permission))
                ;

                $newValues = collect($value);
                $remove = $oldValues->diffUsing($newValues, fn ($a, $b) => $a->name === $b->name ? 0 : -1);
                $add = $newValues->diffUsing($oldValues, fn ($a, $b) => $a->name === $b->name ? 0 : -1);

                DB::table(config('simple-permissions.tables.permissions'))
                    ->where($type, static::class)
                    ->where($id, $this->getKey())
                    ->whereIn('permission', $remove->map(fn ($permission) => SimplePermissions::make($permission)))
                    ->delete()
                ;

                DB::table(config('simple-permissions.tables.permissions'))
                    ->insert($add->map(fn ($permission) => [
                        $type => static::class,
                        $id => $this->getKey(),
                        'permission' => SimplePermissions::make($permission),
                    ])->all())
                ;
            }
        );
    }

    /**
     * Determine if the entity has the given abilities.
     *
     * @param  Permission|Permission[]|iterable|string  $abilities
     * @param  array|mixed  $arguments
     */
    public function can($abilities, $arguments = []): bool
    {
        return parent::can(
            abilities: array_map(fn ($ability) => $ability instanceof Permission
                ? SimplePermissions::make($ability)
                : $ability, Arr::wrap($abilities)),
            arguments: $arguments
        );
    }

    public function hasPermission(string $permission): bool
    {
        return (bool) $this->roles
            ->first(
                fn (Role $role) => in_array($permission, SimplePermissions::getPermissions($role)),
                false
            );
    }
}
