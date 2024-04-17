<?php

namespace Guava\SimplePermissions\Tests\Feature;

use Guava\SimplePermissions\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Workbench\App\Auth\Roles\TestRoleAdmin;
use Workbench\App\Auth\Roles\TestRoleUser;
use Workbench\App\Models\User;

class RolesTest extends TestCase
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

    public function test_can_assign_role()
    {
        $this->assignAndAssert([TestRoleUser::class]);
    }

    public function test_can_assign_multiple_roles()
    {
        $this->assignAndAssert([TestRoleUser::class, TestRoleUser::class]);
    }

    public function test_can_reassign_same_roles()
    {
        $this->assignAndAssert([TestRoleUser::class]);
        $this->assignAndAssert([TestRoleUser::class, TestRoleUser::class]);
    }

    public function test_can_assign_multiple_different_roles()
    {
        $this->assignAndAssert([TestRoleUser::class, TestRoleAdmin::class]);
        $this->assignAndAssert([TestRoleAdmin::class, TestRoleUser::class]);
        $this->assignAndAssert([TestRoleAdmin::class]);
        $this->assignAndAssert([TestRoleUser::class]);
    }

    public function test_can_reassign_roles_via_helpers()
    {
        $this->user->roles = [TestRoleUser::class];
        $this->user->addRole(TestRoleUser::class);
        $this->user->addRole(TestRoleUser::class);
        $this->user->addRole(TestRoleAdmin::class);
        $this->assertEquals(2, $this->user->roles->count());
    }

    public function test_can_remove_roles_via_helpers()
    {

        $this->user->roles = [TestRoleUser::class];
        $this->user->removeRole(TestRoleUser::class);
        $this->user->removeRole(TestRoleUser::class);
        $this->assertEquals(0, $this->user->roles->count());
    }

    protected function assignAndAssert(array $roles): void
    {
        $this->user->roles = $roles;

        foreach ($roles as $role) {
            $role = is_string($role) ? $role : $role::class;
            $this->assertDatabaseHas($this->rolesTable, [
                'roleable_type' => User::class,
                'roleable_id' => $this->user->id,
                'role' => $role,
            ]);

            $this->assertEquals(1, \DB::table($this->rolesTable)
                ->where('roleable_type', User::class)
                ->where('roleable_id', $this->user->id)
                ->where('role', $role)
                ->count());
        }
    }
}
