<?php

namespace Guava\SimplePermissions\Concerns;

use Guava\SimplePermissions\Contracts\Permission;
use Guava\SimplePermissions\Facades\SimplePermissions;
use Guava\SimplePermissions\Models\Permissionable;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasPermissions
{
    public function addPermission(Permission $permission, ?Model $target = null): static
    {
        $query = $this->permissions();

        if ($this->modifyPermissionsQuery($query, $permission, $target)->exists()) {
            return $this;
        }

        $query->create([
            'permission' => $permission,
            'targettable_type' => $target?->getMorphClass(),
            'targettable_id' => $target?->getKey(),
        ]);

        return $this;
    }

    public function removePermission(Permission $permission, ?Model $target = null): static
    {
        /** @var Permissionable $record */
        if ($record = $this->modifyPermissionsQuery($this->permissions(), $permission, $target)->first()) {
            $record->delete();
        }

        return $this;
    }

    public function hasPermission(Permission $permission, ?Model $target = null): bool
    {
        return $this->modifyPermissionsQuery($this->permissions(), $permission, $target)->exists();
    }

    public function getPermissions(?Model $target = null): array
    {
        return $this->permissions()
            ->where('targettable_type', $target?->getMorphClass())
            ->where('targettable_id', $target?->getKey())
            ->get()
            ->pluck('permission')
            ->toArray()
        ;
    }

    //    public function setPermissionsAttribute(array | Collection $permissions): void
    //    {
    //        $permissions = collect($permissions);
    //
    //        $this->permissions()->delete();
    //        $this->permissions()->createMany(
    //            $permissions
    //                ->filter(fn ($permission) => $permission instanceof Permission)
    //                ->map(fn (Permission $permission) => [
    //                    'permission' => $permission,
    //                    config('simple-permissions.tenancy.column') => null,
    //                ])
    //                ->unique()
    //                ->toArray()
    //        );
    //    }

    //    public function getPermissionsAttribute(): Collection
    //    {
    //        return $this->permissions()->pluck('permission');
    //    }

    public function permissions(): MorphMany
    {
        return $this->morphMany(Permissionable::class, 'permissionable');
    }

    private function modifyPermissionsQuery(Builder $query, Permission $permission, ?Model $target = null): Builder
    {
        return $query
            ->where('permission', SimplePermissions::make($permission))
            ->where('targettable_type', $target?->getMorphClass())
            ->where('targettable_id', $target?->getKey())
        ;
    }
}
