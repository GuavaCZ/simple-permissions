<?php

namespace Guava\SimplePermissions\Concerns;

use Guava\SimplePermissions\Contracts\Permission;
use Guava\SimplePermissions\Facades\SimplePermissions;
use Guava\SimplePermissions\Models\Permissionable;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;

trait HasPermissions
{
    public function addPermission(Permission $permission): void
    {
        if ($this->permissions()->where('permission', SimplePermissions::make($permission))->exists()) {
            return;
        }

        $this->permissions()->create([
            'permission' => $permission,
        ]);
    }

    public function removePermission(Permission $permission): void
    {
        /** @var Permissionable $record */
        if ($record = $this->permissions()->where('permission', SimplePermissions::make($permission))->first()) {
            $record->delete();
        }
    }

    public function hasPermission(Permission $permission): bool
    {
        return $this->permissions()
            ->where('permission', SimplePermissions::make($permission))
            ->exists()
        ;
    }

    public function setPermissionsAttribute(array | Collection $permissions): void
    {
        $permissions = collect($permissions);

        $this->permissions()->delete();
        $this->permissions()->createMany(
            $permissions
                ->filter(fn ($permission) => $permission instanceof Permission)
                ->map(fn (Permission $permission) => ['permission' => $permission])
                ->unique()
                ->toArray()
        );
    }

    public function getPermissionsAttribute(): Collection
    {
        return $this->permissions()->pluck('permission');
    }

    public function permissions(): MorphMany
    {
        return $this->morphMany(Permissionable::class, 'permissionable');
    }
}
