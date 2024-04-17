<?php

namespace Guava\SimplePermissions\Models;

use Guava\SimplePermissions\Casts\PermissionCast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Permissionable extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'permission',
    ];

    protected $casts = [
        'permission' => PermissionCast::class,
    ];

    public function permissionable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getTable()
    {
        return config('simple-permissions.tables.permissions', parent::getTable());
    }
}
