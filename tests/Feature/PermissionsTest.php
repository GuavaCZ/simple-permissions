<?php

namespace Guava\SimplePermissions\Tests\Feature;

use Guava\SimplePermissions\Facades\SimplePermissions;
use Guava\SimplePermissions\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Workbench\App\Auth\Permissions\TestPermissions;
use Workbench\App\Models\User;

class PermissionsTest extends TestCase
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

    public function test_can_assign_permission()
    {
        $this->assignAndAssert([
            TestPermissions::CREATE,
        ]);
    }

    public function test_can_assign_multiple_permissions()
    {
        $this->assignAndAssert([
            TestPermissions::CREATE,
            TestPermissions::CREATE,
            TestPermissions::VIEW,
        ]);
    }

    public function test_can_reasasign_permissions()
    {

        $this->assignAndAssert([
            TestPermissions::CREATE,
            TestPermissions::VIEW,
        ]);

        $this->assignAndAssert([
            TestPermissions::CREATE,
            TestPermissions::VIEW,
            TestPermissions::DELETE,
        ]);

        $this->assertEquals(3, $this->user->permissions->count());
    }

    public function test_can_reassign_permissions_via_helpers()
    {
        $this->user->permissions = [TestPermissions::VIEW];
        $this->user->addPermission(TestPermissions::CREATE);
        $this->user->addPermission(TestPermissions::VIEW);
        $this->user->addPermission(TestPermissions::VIEW);
        $this->user->addPermission(TestPermissions::DELETE);
        $this->assertEquals(3, $this->user->permissions->count());
    }

    public function test_can_remove_permissions_via_helpers()
    {
        $this->user->permissions = TestPermissions::cases();

        $this->user->removePermission(TestPermissions::VIEW);
        $this->user->removePermission(TestPermissions::VIEW);
        $this->user->removePermission(TestPermissions::CREATE);
        $this->user->removePermission(TestPermissions::VIEW);

        $this->assertEquals(2, $this->user->permissions->count());
    }

    public function test_can_add_and_remove_permissions_via_helpers()
    {
        $this->user->permissions = [TestPermissions::VIEW];

        $this->user->addPermission(TestPermissions::CREATE);
        $this->user->addPermission(TestPermissions::CREATE);
        $this->user->removePermission(TestPermissions::CREATE);
        $this->user->addPermission(TestPermissions::VIEW);
        $this->user->addPermission(TestPermissions::EDIT);
        $this->user->removePermission(TestPermissions::CREATE);
        $this->user->removePermission(TestPermissions::VIEW);

        $this->assertEquals(1, $this->user->permissions->count());
    }

    protected function assignAndAssert(array $permissions): void
    {
        $this->user->permissions = $permissions;

        foreach ($permissions as $permission) {
            $permission = is_string($permission) ? $permission : SimplePermissions::make($permission);
            $this->assertDatabaseHas($this->permissionsTable, [
                'permissionable_type' => User::class,
                'permissionable_id' => $this->user->id,
                'permission' => $permission,
            ]);

            $this->assertEquals(\DB::table($this->permissionsTable)
                ->where('permissionable_type', User::class)
                ->where('permissionable_id', $this->user->id)
                ->where('permission', $permission)
                ->count(), 1);
        }
    }
}
