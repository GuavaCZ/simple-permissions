<?php

namespace Guava\SimplePermissions\Tests\Feature;

use Guava\SimplePermissions\Facades\SimplePermissions;
use Guava\SimplePermissions\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Workbench\App\Auth\Permissions\TestPermissions;
use Workbench\App\Auth\Roles\TestRoleUser;
use Workbench\App\Models\User;

class GateTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected string $permissionsTable;

    protected string $rolesTable;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name' => fake()->name(),
            'email' => fake()->email(),
            'password' => bcrypt('password'),
        ]);

        $this->permissionsTable = config('simple-permissions.tables.permissions');
        $this->rolesTable = config('simple-permissions.tables.roles');
    }

    public function test_has_role_permission()
    {
        $this->user->roles = [TestRoleUser::class];

        $this->assertTrue($this->user->can(TestPermissions::VIEW));
        $this->assertFalse($this->user->can(TestPermissions::EDIT));
        $this->assertTrue($this->user->hasPermission(SimplePermissions::make(TestPermissions::DELETE)));
    }

    public function test_check_permission_for_user()
    {
        $this->user->roles = [TestRoleUser::class];
        $this->user->permissions = [TestPermissions::EDIT];

        $this->assertTrue($this->user->can(TestPermissions::VIEW));
        $this->assertTrue($this->user->can(TestPermissions::EDIT));
    }
}
