<?php

namespace Guava\SimplePermissions\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use Guava\SimplePermissions\SimplePermissionsServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Guava\\SimplePermissions\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            SimplePermissionsServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

//        $this->loadLaravelMigrations('testing');

//        $this->loadLaravelMigrations(['--database' => 'testing']);
//        $this->artisan('migrate', ['--database' => 'testbench'])->run();
        /*
        $migration = include __DIR__.'/../database/migrations/create_simple-permissions-for-laravel_table.php.stub';
        $migration->up();
        */
    }
}
