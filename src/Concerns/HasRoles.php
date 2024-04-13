<?php

namespace Guava\SimplePermissions\Concerns;

use Guava\SimplePermissions\Contracts\Role;
use Guava\SimplePermissions\Facades\SimplePermissions;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

trait HasRoles
{
    use HasPermissions;

    public function roles(): Attribute
    {
        return Attribute::make(
            get: function () {
                [$type, $id] = $this->getMorphs('roleable', null, null);

                return DB::table(config('simple-permissions.tables.roles'))
                    ->where($type, static::class)
                    ->where($id, $this->getKey())
                    ->pluck('role')
//                    ->map(fn ($permission) => SimplePermissions::permissionFromString($permission))
                    ;
            },
            set: function ($value) {
                [$type, $id] = $this->getMorphs('roleable', null, null);

                $oldValues = DB::table(config('simple-permissions.tables.roles'))
                    ->where($type, static::class)
                    ->where($id, $this->getKey())
                    ->pluck('role')
//                    ->map(fn ($permission) => SimplePermissions::permissionFromString($permission))
                ;

                $newValues = collect($value);
                $remove = $oldValues->diffUsing($newValues, fn ($a, $b) => $a === $b ? 0 : -1);
                $add = $newValues->diffUsing($oldValues, fn ($a, $b) => $a === $b ? 0 : -1);

                DB::table(config('simple-permissions.tables.roles'))
                    ->where($type, static::class)
                    ->where($id, $this->getKey())
                    ->whereIn('role', $remove)
                    ->delete()
                ;

                DB::table(config('simple-permissions.tables.roles'))
                    ->insert($add->map(fn ($role) => [
                        $type => static::class,
                        $id => $this->getKey(),
                        'role' => $role,
                    ])->all())
                ;
            }
        );
    }

    public function addRole(Role | string | array ...$roles): Collection
    {
        $roles = Arr::flatten($roles);

        $newRoles = $this->roles;
        foreach ($roles as $role) {
            $role = is_string($role) ? new $role : $role;

            if ($newRoles->contains(fn (Role $item) => $item::class === $role::class)) {
                continue;
            }

            $newRoles->push($role);
        }
        $this->update(['roles' => $newRoles]);

        return $newRoles;
    }

    public function hasRole(Role | string | array ...$roles): bool
    {
        $roles = Arr::map(Arr::flatten($roles), fn ($role) => is_string($role) ? $role : $role::class);

        return collect($roles)->every(fn ($value) => $this->roles->contains(fn (Role $role) => $role::class === $value));
    }

    public function hasRoleAny(Role | string | array ...$roles): bool
    {
        $roles = Arr::map(Arr::flatten($roles), fn ($role) => is_string($role) ? $role : $role::class);

        return $this->roles->contains(fn ($role) => in_array($role::class, $roles));
    }

    public function removeRole(Role | string | array ...$roles): Collection
    {
        $roles = Arr::map(Arr::flatten($roles), fn ($role) => is_string($role) ? $role : $role::class);

        $newRoles = collect($this->roles)->filter(fn (Role $item) => ! in_array($item::class, $roles));
        $this->update(['roles' => $newRoles]);

        return $newRoles;
    }
}
