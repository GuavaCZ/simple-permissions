<?php

namespace Workbench\App\Auth\Permissions;

use Guava\SimplePermissions\Contracts\Permission;

enum TestPermissions: string implements Permission
{
    case CREATE = 'create';
    case VIEW = 'view';
    case EDIT = 'update';
    case DELETE = 'delete';

}
