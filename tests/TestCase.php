<?php

namespace Guava\SimplePermissions\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\Attributes\WithMigration;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as Orchestra;
use Guava\SimplePermissions\SimplePermissionsServiceProvider;

#[WithMigration('laravel')]
class TestCase extends Orchestra
{
    use WithWorkbench;

    public function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Workbench\\App\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
        // additional setup
    }

    protected function getPackageProviders($app)
    {
        return [
            SimplePermissionsServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        $migration = include __DIR__.'/../database/migrations/create_permissions_table.php.stub';
        $migration->up();
    }
}
