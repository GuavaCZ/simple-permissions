<?php

namespace Guava\SimplePermissions\Concerns;

use Guava\SimplePermissions\Contracts\Permission;
use Guava\SimplePermissions\Contracts\Role;
use Guava\SimplePermissions\Facades\SimplePermissions;
use Guava\SimplePermissions\Models\Roleable;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

trait HasRoles
{
    public function addRole(string|Role $role): void
    {
        $role = $role instanceof Role ? $role::class : $role;

        if ($this->roles()->where('role', $role)->exists()) {
            return;
        }

        $this->roles()->create([
            'role' => $role,
        ]);
    }

    public function removeRole(string|Role $role): void
    {
        $role = $role instanceof Role ? $role::class : $role;
        /** @var Roleable $record */
        if ($record = $this->roles()->where('role', $role)->first()) {
            $record->delete();
        }
    }

    public function hasRole(string|Role $role): bool
    {
        $role = $role instanceof Role ? $role::class : $role;
        return $this->roles()
            ->where('role', $role)
            ->exists();
    }

    public function setRolesAttribute(array | Collection $roles): void
    {
        $roles = collect($roles);

        $this->roles()->delete();
        $this->roles()->createMany(
            $roles
                ->filter(fn (string | Role $role) => $role instanceof Role || class_exists($role))
                ->map(fn (string | Role $role) => ['role' => is_string($role) ? $role : $role::class])
                ->unique()
                ->toArray()
        );
    }

    public function getRolesAttribute(): Collection
    {
        return $this->roles()->pluck('role');
    }

    public function roles(): MorphMany
    {
        return $this->morphMany(Roleable::class, 'roleable');
    }

}
