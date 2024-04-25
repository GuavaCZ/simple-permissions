<?php

namespace Guava\SimplePermissions;

use Guava\SimplePermissions\Commands\MakePermissionCommand;
use Guava\SimplePermissions\Commands\MakeRoleCommand;
use Guava\SimplePermissions\Commands\MakeRoleSetCommand;
use Illuminate\Support\Facades\Gate;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class SimplePermissionsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {

        $package
            ->name('simple-permissions')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_permissions_table')
            ->hasCommand(MakeRoleSetCommand::class)
            ->hasCommand(MakeRoleCommand::class)
            ->hasCommand(MakePermissionCommand::class);
    }

    public function packageBooted(): void
    {
        Gate::before(function ($user, $ability, $arguments) {
            if ($permission = \Guava\SimplePermissions\Facades\SimplePermissions::permissionFromString($ability)) {
                return $user->hasPermission($permission, data_get($arguments, 'target'));
            }
        });
    }
}
