<?php

namespace Guava\SimplePermissions\Concerns;

use Guava\SimplePermissions\Contracts\Permission;
use Guava\SimplePermissions\Contracts\Role;
use Guava\SimplePermissions\Models\Roleable;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;

trait HasRoles
{
    public function addRole(string | Role $role, ?Model $target = null): static
    {
        $role = $role instanceof Role ? $role::class : $role;

        if ($this->modifyRolesQuery($this->roles(), $role, $target)->exists()) {
            return $this;
        }

        $this->roles()->create([
            'role' => $role,
            'targettable_type' => $target?->getMorphClass(),
            'targettable_id' => $target?->getKey(),
        ]);

        return $this;
    }

    public function removeRole(string | Role $role, ?Model $target = null): static
    {
        $role = $role instanceof Role ? $role::class : $role;
        /** @var Roleable $record */
        if ($record = $this->modifyRolesQuery($this->roles(), $role, $target)->first()) {
            $record->delete();
        }

        return $this;
    }

    public function hasRole(string | Role $role, ?Model $target = null): bool
    {
        $role = $role instanceof Role ? $role::class : $role;

        return $this->modifyRolesQuery($this->roles(), $role, $target)->exists();
    }

    public function getRoles(?Model $target = null): Collection
    {
        return $this->roles()
            ->where('targettable_type', $target?->getMorphClass())
            ->where('targettable_id', $target?->getKey())
            ->pluck('role')
        ;
    }

    public function hasPermission(Permission $permission, ?Model $target = null): bool
    {
        return (bool) $this->roles()
            ->where('targettable_type', $target?->getMorphClass())
            ->where('targettable_id', $target?->getKey())
            ->pluck('role')
            ->first(
                fn (Role $role) => in_array($permission, $role->permissions()),
            )
        ;
    }

    //    public function setRolesAttribute(array | Collection $roles): void
    //    {
    //        $roles = collect($roles);
    //
    //        $this->roles()->delete();
    //        $this->roles()->createMany(
    //            $roles
    //                ->filter(fn (string | Role $role) => $role instanceof Role || class_exists($role))
    //                ->map(fn (string | Role $role) => ['role' => is_string($role) ? $role : $role::class])
    //                ->unique()
    //                ->toArray()
    //        );
    //    }

    public function getRolesAttribute(): Collection
    {
        return $this->roles()->pluck('role');
    }

    public function roles(): MorphMany
    {
        return $this->morphMany(Roleable::class, 'roleable');
    }

    private function modifyRolesQuery(Builder $query, string $role, ?Model $target = null): Builder
    {
        return $query
            ->where('role', $role)
            ->where('targettable_type', $target?->getMorphClass())
            ->where('targettable_id', $target?->getKey())
        ;
    }
}
