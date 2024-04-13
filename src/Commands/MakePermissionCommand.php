<?php

namespace Guava\SimplePermissions\Commands;

use Illuminate\Console\GeneratorCommand;

class MakePermissionCommand extends GeneratorCommand
{
    protected $name = 'make:permission';

    public $description = 'Creates a new permissions';

    protected $type = 'Permission';

    public function handle(): int
    {
        parent::handle();
        $this->comment('All done');

        return self::SUCCESS;
    }

    protected function getStub(): string
    {
        return __DIR__ . '/../../stubs/permission.php.stub';
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . '\Auth\Permissions';
    }
}
