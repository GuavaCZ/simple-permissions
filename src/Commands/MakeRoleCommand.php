<?php

namespace Guava\SimplePermissions\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeRoleCommand extends GeneratorCommand
{
    protected $name = 'make:role';

    public $description = 'Creates a new role';

    protected $type = 'Role';

    public function handle(): int
    {
        parent::handle();
        $this->comment('All done');

        return self::SUCCESS;
    }

    protected function getStub(): string
    {
        return __DIR__ . '/../../stubs/role.php.stub';
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . '\Auth\Roles';
    }
}
