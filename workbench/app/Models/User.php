<?php

namespace Workbench\App\Models;

use Guava\SimplePermissions\Concerns\HasAccessControl;
use Illuminate\Foundation\Auth\User as BaseUser;

class User extends BaseUser
{
    use HasAccessControl;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];
}
