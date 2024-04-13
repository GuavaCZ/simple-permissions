<?php

namespace Guava\SimplePermissions\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeRoleSetCommand extends GeneratorCommand
{
    protected $name = 'make:role-set';

    public $description = 'Creates a new role set';

    protected $type = 'RoleSet';

    public function handle(): int
    {
        parent::handle();
        $this->comment('All done');

        return self::SUCCESS;
    }

    protected function getStub(): string
    {
        return __DIR__ . '/../../stubs/role_set.php.stub';
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . '\Auth\RoleSets';
    }
}
